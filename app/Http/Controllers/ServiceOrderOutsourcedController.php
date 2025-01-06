<?php

namespace App\Http\Controllers;

use App\Models\ServiceAppointment;
use App\Models\ServiceOrder;
use App\Models\ServiceOrderOutsourced;
use App\Models\ServiceOrderProcess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceOrderOutsourcedController extends Controller
{
    /**
     * Display a listing of the outsourced items.
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
        $paginate = $request->get('paginate', 10);

        // Determine the page number for each pagination
        $page = $request->get('page', 1);

        // Query to retrieve outsourced items
        $outsourcedItems = ServiceOrderOutsourced::where('service_order_id', $service_order->id)
            ->orderBy($order, $by)
            ->paginate($paginate, ['*'], 'page', $page)
            ->appends([
                'order' => $order,
                'by' => $by,
                'paginate' => $paginate,
                'page' => $page,
            ]);

        // Return view with service orders and filters
        return Inertia::render('ServiceOrder/Outsourced/View', [
            'csrf' => csrf_token(),
            'serviceOrder' => $service_order->load(['serviceQuotation', 'technician', 'customer', 'vehicle.salesOrders.refrigerationSale', 'currentProcess']),
            'outsourcedItems' => $outsourcedItems,
            'stages' => ServiceOrderProcess::STAGES,
            'types' => ServiceAppointment::TYPES,
        ]);
    }

    /**
     * Store a newly created outsourced item in storage.
     *
     * @param Request $request
     * @param ServiceOrder $service_order
     * @return RedirectResponse
     */
    public function store(Request $request, ServiceOrder $service_order): RedirectResponse
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

        $service_order->outsourced()->create([
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
     * @param ServiceOrderOutsourced $outsourced
     * @return RedirectResponse
     */
    public function update(Request $request, ServiceOrderOutsourced $outsourced): RedirectResponse
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
     * @param ServiceOrder $serviceOrder
     * @param ServiceOrderOutsourced $outsourced
     * @return RedirectResponse
     */
    public function destroy(ServiceOrder $serviceOrder, ServiceOrderOutsourced $outsourced): RedirectResponse
    {
        $outsourced->delete();

        return redirect()->back()->with('success', 'Outsourced item deleted successfully.');
    }
}
