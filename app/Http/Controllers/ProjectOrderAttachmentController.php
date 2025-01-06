<?php

namespace App\Http\Controllers;

use App\Models\ProjectOrder;
use App\Models\ProjectOrderAttachment;
use App\Models\ProjectOrderProcess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectOrderAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ProjectOrder $project_order
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(ProjectOrder $project_order, Request $request): \Inertia\Response
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
        $attachments = ProjectOrderAttachment::whereIn('stage', [2, 3, 4, 5])
            ->where('project_order_id', $project_order->id)
            ->orderBy($order, $by)
            ->paginate($attachmentsPaginate, ['*'], 'page', $attachmentsPage)
            ->appends([
                'order' => $order,
                'by' => $by,
                'paginate' => $attachmentsPaginate,
                'page' => $attachmentsPage,
            ]);

        $firstAttachments = ProjectOrderAttachment::where('stage', 1)
            ->where('project_order_id', $project_order->id)
            ->orderBy($order, $by)
            ->paginate($firstAttachmentsPaginate, ['*'], 'first_page', $firstAttachmentsPage)
            ->appends([
                'order' => $order,
                'by' => $by,
                'first_paginate' => $firstAttachmentsPaginate,
                'first_page' => $firstAttachmentsPage,
            ]);

        // Return view with project orders and filters
        return Inertia::render('ProjectOrder/Attachment/View', [
            'csrf' => csrf_token(),
            'projectOrder' => $project_order->load(['projectQuotation', 'technician', 'customer', 'vehicles', 'currentProcess']),
            'stages' => ProjectOrderProcess::STAGES,
            'firstAttachments' => $firstAttachments,
            'attachments' => $attachments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param ProjectOrder $project_order
     * @return RedirectResponse
     */
    public function store(Request $request, ProjectOrder $project_order): RedirectResponse
    {
        $request->validate([
            'file_url' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'remark' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('file_url')) {
            $file = $request->file('file_url');
            $path = $file->store('attachments', 'public');

            $attachment = new ProjectOrderAttachment([
                'stage' => $project_order->stage,
                'name' => $file->getClientOriginalName(),
                'file_url' => $path,
                'remarks' => $request->input('remarks'),
            ]);

            $project_order->attachments()->save($attachment);
        }

        return redirect()->back()->with('success', 'Attachment uploaded successfully.');
    }
}
