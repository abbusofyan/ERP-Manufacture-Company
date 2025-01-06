<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Contractor;
use App\Models\ContractorItem;
use App\Models\Customer;
use App\Models\Milestone;
use App\Models\MilestoneItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductionWorkOrder;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationHeaderType;
use App\Models\QuotationHeaderTypeItem;
use App\Models\QuotationRemark;
use App\Models\RefrigerationSale;
use App\Models\SalesOrderECO;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CustomerVehicleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Customer::class, 'customer');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Customer $customer
     * @param Request $request
     * @return Response
     */
    public function index(Customer $customer, Request $request): Response
    {
        $search = $request->search;

        if ($request->order && $request->by) {
            $order = $request->order;
            $by = $request->by;
        } else {
            $order = 'id';
            $by = 'desc';
        }

        if ($request->paginate) {
            $paginate = $request->paginate;
        } else {
            $paginate = 10;
        }

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $vehicles = Vehicle::with('customer')
            ->when($search, function ($query) use ($search) {
                $query->where('license_plate', 'LIKE', "%{$search}%");
                $query->where('type', 'LIKE', "%{$search}%");
                $query->where('end_user', 'LIKE', "%{$search}%");
            })
            ->where('customer_id', $customer->id)
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];

        $vehicles->appends($queryString);

        return Inertia::render('Customer/Vehicle/View', [
            'customer' => $customer,
            'vehicles' => $vehicles,
            'filters' => $filters,
        ]);
    }
}
