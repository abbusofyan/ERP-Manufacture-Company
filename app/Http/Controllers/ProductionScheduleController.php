<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductionScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param null $production_order
     * @return Response
     */
    public function index(Request $request, $production_order = null): Response
    {
        $production_order_id = $request->production_order_id;

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
        $filters['production_order_id'] = $production_order_id;

        // Query to retrieve service orders with related entities
        $productionOrders = ProductionOrder::with(['quotation'])
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
        $productionOrders->appends($queryString);

        //Get process of service order
        $productionOrder = null;
        if ($request->has('production_order_id')) {
            $productionOrder = ProductionOrder::find($production_order_id)->load('processes.timelogs', 'processes');
        }

        // Return view with service orders and filters
        return Inertia::render('ProductionOrder/Schedule/Schedule', [
            'csrf' => csrf_token(),
            'productionOrder' => $productionOrder,
            'productionOrders' => $productionOrders,
            'filters' => $filters,
        ]);
    }
}
