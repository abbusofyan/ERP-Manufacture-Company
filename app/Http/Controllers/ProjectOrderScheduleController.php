<?php

namespace App\Http\Controllers;

use App\Models\ProjectOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectOrderScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ProjectOrder $project_order
     * @return Response
     */
    public function index(Request $request, $project_order = null): Response
    {
        $project_order_id = $request->project_order_id;

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
        $filters['project_order_id'] = $project_order_id;

        // Query to retrieve project orders with related entities
        $projectOrders = ProjectOrder::with(['technician', 'customer', 'vehicle', 'picCountryPhoneCode', 'requirements.storageItem.product'])
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
        $projectOrders->appends($queryString);

        //Get process of project order
        $project_order = null;
        if($request->has('project_order_id')){
            $project_order = ProjectOrder::find($project_order_id)?->load('projectOrderProcess.timelogs');
        }

        // Return view with project orders and filters
        return Inertia::render('ProjectOrder/Schedule/Schedule', [
            'csrf' => csrf_token(),
            'projectOrder' => $project_order,
            'projectOrders' => $projectOrders,
            'filters' => $filters,
        ]);
    }
}
