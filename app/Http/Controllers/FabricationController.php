<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use App\Models\SalesOrderECO;
use App\Models\Product;
use App\Models\EngineeringOrder;
use App\Models\EngineeringOrderMaterial;
use App\Models\EngineeringOrderProcess;
use App\Models\RefrigerationSaleSpecificationItem;
use App\Models\Process;
use App\Models\EngineeringOrderAttachment;
use App\Models\Fabrication;
use App\Models\FabricationItem;
use Inertia\Inertia;
use Inertia\Response;
use DB;

class FabricationController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Assembly::class, 'assembly');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
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

        $orders = Fabrication::with(['product', 'assembly'])->when($search, function ($query) use ($search) {
            $query->where('code', 'LIKE', "%{$search}%");
        })
        ->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);
        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];

        $orders->appends($queryString);
        return Inertia::render('Fabrication/Index', [
            'orders' => $orders,
            'filters' => [],
        ]);
    }


    public function create() {
        $products = Product::with('uom')->get();

        return Inertia::render('Fabrication/Form', [
            'products' => $products,
            'csrf' => csrf_token(),
        ]);
    }

    public function store(Request $request) {
        $order = Fabrication::create([
            'code' => Fabrication::generateCode(),
            'type' => $request->type
        ]);
        foreach ($request->item_input as $input) {
            $item = FabricationItem::create([
                'fabrication_id' => $order->id,
                'product_id' => $input['product_id'],
                'type' => 'Input',
                'qty' => $input['qty']
            ]);
        }

        foreach ($request->item_output as $output) {
            $item = FabricationItem::create([
                'fabrication_id' => $order->id,
                'product_id' => $output['product_id'],
                'type' => 'Output',
                'qty' => $output['qty']
            ]);
        }
        return redirect('/fabrication')->with('updated', 'Order Created');
    }


    public function show(Request $request, Fabrication $fabrication)
    {
        $inputItems = FabricationItem::with('product.uom')->where([
            'fabrication_id' => $fabrication->id,
            'type' => 'Input'
        ])->get();

        $outputItems = FabricationItem::with('product.uom')->where([
            'fabrication_id' => $fabrication->id,
            'type' => 'Output'
        ])->get();

        return Inertia::render('Fabrication/Detail', [
            'order' => $fabrication,
            'input_items' => $inputItems,
            'output_items' => $outputItems
        ]);
    }


}
