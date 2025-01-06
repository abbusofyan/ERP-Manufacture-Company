<?php

namespace App\Http\Controllers;

use App\Models\ProjectOrder;
use App\Models\ProjectOrderOutsourced;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectOrderOutsourcedController extends Controller
{
    /**
     * Display a listing of the outsourced items.
     *
     * @param ProjectOrder $project_order
     * @param Request $request
     * @return Response
     */
    public function index(ProjectOrder $project_order, Request $request): Response
    {
        // Determine order and sort direction
        $order = $request->get('order', 'id');
        $by = $request->get('by', 'desc');

        // Determine pagination limit
        $paginate = $request->get('paginate', 10);

        // Determine the page number for each pagination
        $page = $request->get('page', 1);

        // Query to retrieve outsourced items
        $outsourcedItems = ProjectOrderOutsourced::where('project_order_id', $project_order->id)
            ->orderBy($order, $by)
            ->paginate($paginate, ['*'], 'page', $page)
            ->appends([
                'order' => $order,
                'by' => $by,
                'paginate' => $paginate,
                'page' => $page,
            ]);

        // Return view with project orders and filters
        return Inertia::render('ProjectOrder/Outsourced/View', [
            'csrf' => csrf_token(),
            'projectOrder' => $project_order->load(['projectQuotation', 'technician', 'customer', 'vehicles', 'currentProcess']),
            'outsourcedItems' => $outsourcedItems,
        ]);
    }

    /**
     * Store a newly created outsourced item in storage.
     *
     * @param Request $request
     * @param ProjectOrder $project_order
     * @return RedirectResponse
     */
    public function store(Request $request, ProjectOrder $project_order): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'file_url' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('file_url')) {
            $file = $request->file('file_url');
            $filePath = $file->store('outsourced_files', 'public');
        }

        $project_order->outsourced()->create([
            'name' => $request->input('name'),
            'quantity' => $request->input('quantity'),
            'price' => $request->input('price'),
            'file_url' => $filePath,
        ]);

        return redirect()->back()->with('success', 'Outsourced item added successfully.');
    }

    /**
     * Update the specified outsourced item in storage.
     *
     * @param Request $request
     * @param ProjectOrderOutsourced $outsourced
     * @return RedirectResponse
     */
    public function update(Request $request, ProjectOrderOutsourced $outsourced): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'file_url' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $filePath = $outsourced->file_url;
        if ($request->hasFile('file_url')) {
            $file = $request->file('file_url');
            $filePath = $file->store('outsourced_files', 'public');
        }

        $outsourced->update([
            'name' => $request->input('name'),
            'quantity' => $request->input('quantity'),
            'price' => $request->input('price'),
            'file_url' => $filePath,
        ]);

        return redirect()->back()->with('success', 'Outsourced item updated successfully.');
    }

    /**
     * Remove the specified outsourced item from storage.
     *
     * @param ProjectOrder $projectOrder
     * @param ProjectOrderOutsourced $outsourced
     * @return RedirectResponse
     */
    public function destroy(ProjectOrder $projectOrder, ProjectOrderOutsourced $outsourced): RedirectResponse
    {
        $outsourced->delete();

        return redirect()->back()->with('success', 'Outsourced item deleted successfully.');
    }
}
