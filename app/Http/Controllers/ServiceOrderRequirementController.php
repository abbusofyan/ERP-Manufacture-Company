<?php

namespace App\Http\Controllers;

use App\Models\ServiceAppointment;
use App\Models\Storage;
use App\Models\StorageItem;
use App\Models\Product;
use App\Models\ServiceOrder;
use App\Models\ServiceOrderProcess;
use App\Models\ServiceOrderRequirement;
use App\Models\ServiceOrderRequirementUsed;
use App\Models\UnitOfMeasurement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ServiceOrderRequirementController extends Controller
{

    public function select2Query(Request $request)
    {
        return ServiceOrderRequirement::when($request->has('search'), function ($query) use ($request) {
            $search = $request->search;
            $query->whereHas('requirement.storageItem.product', function ($query) use ($search) {
                $query->where('sku', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%");
            });
        })
            ->when($request->has('service_order_id'), function ($query) use ($request) {
                $query->where('service_order_id', $request->service_order_id);
            })
            ->with(['storageItem.storage', 'storageItem.product'])
            ->limit(10)
            ->get();
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param ServiceOrder $service_order
     * @return Response
     */
    public function index(Request $request, ServiceOrder $service_order): Response
    {
        // Retrieve search keyword from request
        $search = $request->search;

        // Determine order and sort direction
        $order = $request->order ?? 'id';
        $by = $request->by ?? 'desc';

        // Determine pagination limit
        $paginate = $request->paginate ?? 10;

        // Filters to pass back to the view
        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        // Query to retrieve service order requirements with related entities
        $requirements = ServiceOrderRequirement::where('service_order_id', $service_order->id)
            ->with(['storageItem.storage', 'storageItem.product.uom'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('storageItem.product', function ($query) use ($search) {
                    $query->where('sku', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%");
                });
            })
            ->when($order, function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })
            ->paginate($paginate);

        // Append query string parameters for pagination
        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];
        $requirements->appends($queryString);


        return Inertia::render('ServiceOrder/Requirement/View', [
            'csrf' => csrf_token(),
            'stages' => ServiceOrderProcess::STAGES,
            'types' => ServiceOrder::TYPES,
            'serviceOrder' => $service_order->load(['serviceQuotation', 'technician', 'customer', 'vehicle.salesOrders.refrigerationSale', 'currentProcess']),
            'requirements' => $requirements,
            'filters' => $filters,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param ServiceOrder $service_order
     * @return RedirectResponse
     */
    public function store(Request $request, ServiceOrder $service_order): RedirectResponse
    {
        // Validate incoming request data
        $request->validate([
            'storage_item_id' => 'required|exists:storage_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Retrieve the storage item and requirement
        $storageItem = StorageItem::findOrFail($request->input('storage_item_id'));
        $requirement = $service_order->requirements()->where('storage_item_id', $storageItem->id)->first();
        $maxQty = $storageItem->quantity - ($requirement->quantity ?? 0);

        // Check if the requested quantity is available
        if ($request->input('quantity') > $maxQty) {
            return redirect()->back()->withErrors(['quantity' => "Quantity exceeds available quantity (<= $maxQty)."]);
        }

        // Update existing requirement or create a new one
        $data = ['quantity' => ($requirement ? $requirement->quantity + $request->input('quantity') : $request->input('quantity'))];

        if ($requirement) {
            $requirement->update($data);
        } else {
            $data['storage_item_id'] = $request->input('storage_item_id');
            $service_order->requirements()->create($data);
        }

        // Redirect with a success message
        return redirect()->back()->with('success', 'Requirement updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param ServiceOrder $service_order
     * @param ServiceOrderRequirement $requirement
     * @return RedirectResponse
     */
    public function destroy(ServiceOrder $service_order, ServiceOrderRequirement $requirement): RedirectResponse
    {
        // Delete the service order
        $requirement->delete();

        // Redirect back with success message
        return redirect("/service-orders/$service_order->id")->with('deleted', 'Service order requirement deleted successfully.');
    }
}
