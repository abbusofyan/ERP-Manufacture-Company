<?php

namespace App\Http\Controllers;

use App\Models\ServiceInvoice;
use App\Models\ServiceInvoicePaid;
use App\Models\ServiceQuotation;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ServiceInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
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

        $invoices = ServiceInvoice::with('serviceQuotation.vehicle', 'serviceQuotation.serviceOrder', 'serviceQuotation.customer')
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

        return Inertia::render('ServiceInvoice/Index', [
            'serviceInvoices' => $invoices,
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param ServiceInvoice $service_invoice
     * @return Response
     */
    public function show(Request $request, ServiceInvoice $service_invoice): Response
    {
        // Load only the vehicle parts associated with the current service_invoice
        $service_invoice->load([
            'serviceQuotation.serviceOrder',
            'serviceQuotation.customer',
            'serviceQuotation.vehicle',
            'serviceQuotation.vehicleParts' => function ($query) use ($service_invoice) {
                $query->where('service_invoice_id', $service_invoice->id);  // Filter by service_invoice_id
            },
            'serviceQuotation.vehicleParts.storageItem'
        ]);

        $order = $request->order ?? 'id';
        $by = $request->by ?? 'desc';
        $paginate = $request->paginate ?? 10;
        $filters['paginate'] = $paginate;

        $paids = ServiceInvoicePaid::orderBy($order, $by)
            ->paginate($paginate);

        $paids->appends([
            'paginate' => $paginate,
        ]);

        return Inertia::render('ServiceInvoice/Detail', [
            'serviceInvoice' => $service_invoice,
            'paids' => $paids,
            'filters' => $filters,
        ]);
    }

    /**
     * Store a newly created paid invoice in storage.
     *
     * @param Request $request
     * @param ServiceInvoice $service_invoice
     * @return RedirectResponse
     */
    public function storePaid(Request $request, ServiceInvoice $service_invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'mode_of_payment' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['service_invoice_id'] = $service_invoice->id;

        ServiceInvoicePaid::create($data);

        // Update the status of the service invoice
        $totalPaidAmount = $service_invoice->paids()->sum('amount');
        if ($totalPaidAmount >= $service_invoice->serviceQuotation->total) {
            $service_invoice->update(['status' => ServiceInvoice::STATUS_PAID]);
        } elseif ($totalPaidAmount > 0) {
            $service_invoice->update(['status' => ServiceInvoice::STATUS_PARTIAL_PAID]);
        } else {
            $service_invoice->update(['status' => ServiceInvoice::STATUS_NOT_INVOICED]);
        }

        return redirect()->back()->with('created', 'Payment recorded successfully.');
    }

    /**
     * Store a newly created paid invoice in storage.
     *
     * @param Request $request
     * @param ServiceInvoice $service_invoice
     * @return void
     */
    public function generateDeliveryOrder(Request $request, ServiceInvoice $service_invoice)
    {
        // Save the ServiceInvoice record
        $service_quotation = $service_invoice->serviceQuotation;
        // Render the Blade view as HTML
        $html = view('pdf.service_delivery_order_invoice', [
            'quotation' => $service_quotation->load([
                'serviceOrder.confirmed', 'serviceOrder.technician',
                'customer.countryPhoneCode', 'customer.country',
                'vehicle', 'vehicleParts.storageItem.product.uom'
            ]),
            'invoice' => $service_invoice, // Pass the invoice to the view
        ])->render();

        // Generate PDF from the rendered HTML
        $pdf = PDF::loadHTML($html)->setPaper('a4', 'landscape');

        // Save the PDF to the storage
        $fileSave = "Invoice-PO#{$service_quotation->code}-" . time() . ".pdf";
        $filePathSave = "service_invoices/{$service_invoice->id}/$fileSave";
        Storage::disk('public')->put($filePathSave, $pdf->output());

        $fileStore = urlencode($fileSave);
        $filePathStore = "/storage/service_invoices/{$service_invoice->id}/$fileStore";

        // Update the invoice with calculated amounts
        $service_invoice->update([
            'delivery_order_invoice_url' => $filePathStore
        ]);

        return back()->with('updated', 'PO invoice saved successfully');
    }

    public function update(Request $request, ServiceInvoice $serviceInvoice)
    {
        // Validation rules
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

        // Extract the validated data
        $data = $request->only([
            'finance_amount',
            'deposit_required',
            'rounding',
            'freight_charges',
            'customer_reference_number',
            'amend_customer_address',
            'footer',
        ]);

        // Handle file upload for attachment if present
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store("service_invoice/$serviceInvoice->id", 'public');
            $data['attachment'] = $path;
        }

        // Update the service invoice with the new data
        $serviceInvoice->update($data);

        // Redirect back with a success message
        return back()->with('updated', 'Service invoice updated successfully.');
    }
}
