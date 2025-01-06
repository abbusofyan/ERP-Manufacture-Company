<?php

namespace App\Http\Controllers;

use App\Models\EngineeringOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EngineeringScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param null $engineering_order
     * @return Response
     */
    public function index(Request $request, $engineering_order = null): Response
    {
        $engineering_order_id = $request->engineering_order_id;

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
        $filters['engineering_order_id'] = $engineering_order_id;

        // Query to retrieve engineering orders with related entities
        $engineeringOrders = EngineeringOrder::with(['productionOrder.quotation'])
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
        $engineeringOrders->appends($queryString);

        // Get process of the selected engineering order
        $engineeringOrder = null;
        if ($request->has('engineering_order_id')) {
            $engineeringOrder = EngineeringOrder::find($engineering_order_id)->load('productionOrder.processes.timelogs', 'productionOrder.processes');
        }

        // Return view with engineering orders and filters
        return Inertia::render('EngineeringOrder/Schedule/Schedule', [
            'csrf' => csrf_token(),
            'engineeringOrder' => $engineeringOrder,
            'engineeringOrders' => $engineeringOrders,
            'filters' => $filters,
        ]);
    }
}
