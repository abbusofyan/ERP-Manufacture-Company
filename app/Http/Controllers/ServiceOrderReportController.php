<?php

namespace App\Http\Controllers;

use App\Models\ServiceAppointment;
use App\Models\ServiceOrder;
use App\Models\ServiceOrderProcess;
use App\Models\ServiceOrderReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceOrderReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ServiceOrder $service_order
     * @param Request $request
     * @return Response
     */
    public function index(ServiceOrder $service_order, Request $request): Response
    {
        // Determine order and sort direction
        $order = $request->get('order', 'id');
        $by = $request->get('by', 'desc');

        // Determine pagination limit
        $attachmentsPaginate = $request->get('paginate', 10);

        // Determine the page number for each pagination
        $attachmentsPage = $request->get('page', 1);

        // Query to retrieve attachments with different stages
        $attachments = ServiceOrderReport::where('service_order_id',$service_order->id)
            ->orderBy($order, $by)
            ->paginate($attachmentsPaginate, ['*'], 'page', $attachmentsPage)
            ->appends([
                'order' => $order,
                'by' => $by,
                'paginate' => $attachmentsPaginate,
                'page' => $attachmentsPage,
            ]);

        // Return view with service orders and filters
        return Inertia::render('ServiceOrder/Report/View', [
            'csrf' => csrf_token(),
            'serviceOrder' => $service_order->load(['serviceQuotation', 'technician', 'customer', 'vehicle.salesOrders.refrigerationSale', 'currentProcess']),
            'stages' => ServiceOrderProcess::STAGES,
            'types' => ServiceAppointment::TYPES,
            'attachments' => $attachments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param ServiceOrder $service_order
     * @return RedirectResponse
     */
    public function store(Request $request, ServiceOrder $service_order)
    {
        $request->validate([
            'file_url' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'remark' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('file_url')) {
            $file = $request->file('file_url');
            $path = $file->store('reports', 'public');

            $attachment = new ServiceOrderReport([
                'name' => $file->getClientOriginalName(),
                'file_url' => $path,
                'remarks' => $request->input('remarks'),
            ]);

            $service_order->attachments()->save($attachment);
        }

        return redirect()->back()->with('success', 'Attachment uploaded successfully.');
    }
}
