<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceOrderScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ServiceOrder $service_order
     * @return Response
     */
    public function index(Request $request, $service_order = null): Response
    {
        $service_order_id = $request->service_order_id;

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
        $filters['service_order_id'] = $service_order_id;

        // Query to retrieve service orders with related entities
        $serviceOrders = ServiceOrder::with(['technician', 'customer', 'vehicle', 'picCountryPhoneCode', 'requirements.storageItem.product'])
            ->when($search, function ($query) use ($search) {
                $query
                    ->where('id', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('requirements.storageItem.product', function ($query) use ($search) {
                        $query->where('sku', 'LIKE', "%{$search}%")
                            ->orWhere('name', 'LIKE', "%{$search}%");
                    });
            })
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        // Append query string parameters for pagination
        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];
        $serviceOrders->appends($queryString);

        //Get process of service order
        $service_order = null;
        if($request->has('service_order_id')){
            $service_order = ServiceOrder::find($service_order_id)?->load('serviceOrderProcess.timelogs');
        }

        // Return view with service orders and filters
        return Inertia::render('ServiceOrder/Schedule/Schedule', [
            'csrf' => csrf_token(),
            'serviceOrder' => $service_order,
            'serviceOrders' => $serviceOrders,
            'filters' => $filters,
        ]);
    }
}
