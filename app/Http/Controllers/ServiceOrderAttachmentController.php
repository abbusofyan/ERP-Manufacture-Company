<?php

namespace App\Http\Controllers;

use App\Models\ServiceAppointment;
use App\Models\ServiceOrder;
use App\Models\ServiceOrderAttachment;
use App\Models\ServiceOrderProcess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceOrderAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ServiceOrder $service_order
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(ServiceOrder $service_order, Request $request): \Inertia\Response
    {
        // Determine order and sort direction
        $order = $request->get('order', 'id');
        $by = $request->get('by', 'desc');

        // Determine pagination limit
        $firstAttachmentsPaginate = $request->get('first_paginate', 10);
        $attachmentsPaginate = $request->get('paginate', 10);

        // Determine the page number for each pagination
        $firstAttachmentsPage = $request->get('first_page', 1);
        $attachmentsPage = $request->get('page', 1);

        // Query to retrieve attachments with different stages
        $attachments = ServiceOrderAttachment::whereIn('stage', [2, 3, 4, 5])
            ->where('service_order_id', $service_order->id)
            ->orderBy($order, $by)
            ->paginate($attachmentsPaginate, ['*'], 'page', $attachmentsPage)
            ->appends([
                'order' => $order,
                'by' => $by,
                'paginate' => $attachmentsPaginate,
                'page' => $attachmentsPage,
            ]);

        $firstAttachments = ServiceOrderAttachment::where('stage', 1)
            ->where('service_order_id', $service_order->id)
            ->orderBy($order, $by)
            ->paginate($firstAttachmentsPaginate, ['*'], 'first_page', $firstAttachmentsPage)
            ->appends([
                'order' => $order,
                'by' => $by,
                'first_paginate' => $firstAttachmentsPaginate,
                'first_page' => $firstAttachmentsPage,
            ]);

        // Return view with service orders and filters
        return Inertia::render('ServiceOrder/Attachment/View', [
            'csrf' => csrf_token(),
            'serviceOrder' => $service_order->load(['serviceQuotation', 'technician', 'customer', 'vehicle.salesOrders.refrigerationSale', 'currentProcess']),
            'stages' => ServiceOrderProcess::STAGES,
            'types' => ServiceAppointment::TYPES,
            'firstAttachments' => $firstAttachments,
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
            $path = $file->store("$service_order->id/attachments", 'public');

            $attachment = new ServiceOrderAttachment([
                'stage' => $service_order->stage ?? 1,
                'name' => $file->getClientOriginalName(),
                'file_url' => $path,
                'remarks' => $request->input('remarks'),
            ]);

            $service_order->attachments()->save($attachment);
        }

        return redirect()->back()->with('success', 'Attachment uploaded successfully.');
    }
}
