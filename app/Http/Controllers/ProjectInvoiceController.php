<?php

namespace App\Http\Controllers;

use App\Models\ProjectInvoice;
use App\Models\ProjectInvoicePaid;
use App\Models\ProjectQuotation;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProjectInvoiceController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->search;
        $order = $request->order ?? 'id';
        $by = $request->by ?? 'desc';
        $paginate = $request->paginate ?? 10;

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $invoices = ProjectInvoice::with('projectQuotation.vehicle', 'projectQuotation.projectOrder', 'projectQuotation.customer')
            ->when($search, function ($query) use ($search) {
                $query->where('code', 'LIKE', "%{$search}%");
            })
            ->orderBy($order, $by)
            ->paginate($paginate);

        $invoices->appends([
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ]);

        return Inertia::render('ProjectInvoice/Index', [
            'projectInvoices' => $invoices,
            'filters' => $filters,
        ]);
    }

    public function show(Request $request, ProjectInvoice $project_invoice): Response
    {
        $project_invoice->load([
            'projectQuotation.projectOrder',
            'projectQuotation.customer',
            'projectQuotation.vehicle',
            'projectQuotation.vehicleParts' => function ($query) use ($project_invoice) {
                $query->where('project_invoice_id', $project_invoice->id);
            },
            'projectQuotation.vehicleParts.storageItem'
        ]);

        $order = $request->order ?? 'id';
        $by = $request->by ?? 'desc';
        $paginate = $request->paginate ?? 10;
        $filters['paginate'] = $paginate;

        $paids = ProjectInvoicePaid::orderBy($order, $by)->paginate($paginate);
        $paids->appends(['paginate' => $paginate]);

        return Inertia::render('ProjectInvoice/Detail', [
            'projectInvoice' => $project_invoice,
            'paids' => $paids,
            'filters' => $filters,
        ]);
    }

    public function storePaid(Request $request, ProjectInvoice $project_invoice): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'mode_of_payment' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['project_invoice_id'] = $project_invoice->id;

        ProjectInvoicePaid::create($data);

        $totalPaidAmount = $project_invoice->paids()->sum('amount');
        if ($totalPaidAmount >= $project_invoice->projectQuotation->total) {
            $project_invoice->update(['status' => ProjectInvoice::STATUS_PAID]);
        } elseif ($totalPaidAmount > 0) {
            $project_invoice->update(['status' => ProjectInvoice::STATUS_PARTIAL_PAID]);
        } else {
            $project_invoice->update(['status' => ProjectInvoice::STATUS_NOT_INVOICED]);
        }

        return redirect()->back()->with('created', 'Payment recorded successfully.');
    }

    public function generateDeliveryOrder(Request $request, ProjectInvoice $project_invoice): RedirectResponse
    {
        $project_quotation = $project_invoice->projectQuotation;

        $html = view('pdf.project_delivery_order_invoice', [
            'quotation' => $project_quotation->load([
                'projectOrder.confirmed', 'projectOrder.technician',
                'customer.countryPhoneCode', 'customer.country',
                'vehicle', 'vehicleParts.storageItem.product.uom'
            ]),
            'invoice' => $project_invoice,
        ])->render();

        $pdf = PDF::loadHTML($html)->setPaper('a4', 'landscape');
        $fileSave = "Invoice-PO#{$project_quotation->code}-" . time() . ".pdf";
        $filePathSave = "project_invoices/{$project_invoice->id}/$fileSave";
        Storage::disk('public')->put($filePathSave, $pdf->output());

        $fileStore = urlencode($fileSave);
        $filePathStore = "/storage/project_invoices/{$project_invoice->id}/$fileStore";

        $project_invoice->update(['delivery_order_invoice_url' => $filePathStore]);

        return back()->with('updated', 'PO invoice saved successfully');
    }

    public function update(Request $request, ProjectInvoice $projectInvoice): RedirectResponse
    {
        $request->validate([
            'finance_amount' => 'nullable|numeric|min:0',
            'deposit_required' => 'nullable|numeric|min:0',
            'rounding' => 'nullable|numeric',
            'freight_charges' => 'nullable|numeric|min:0',
            'customer_reference_number' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf|max:2048',
            'amend_customer_address' => 'nullable|string|max:255',
            'footer' => 'nullable|string|max:255',
        ], [
            'attachment.mimes' => 'The attachment must be a PDF file.',
            'attachment.max' => 'The attachment file size must not exceed 2MB.',
        ]);

        $data = $request->only([
            'finance_amount',
            'deposit_required',
            'rounding',
            'freight_charges',
            'customer_reference_number',
            'amend_customer_address',
            'footer',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store("project_invoice/$projectInvoice->id", 'public');
            $data['attachment'] = $path;
        }

        $projectInvoice->update($data);

        return back()->with('updated', 'Project invoice updated successfully.');
    }
}
