<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationHeaderType;
use App\Models\QuotationHeaderTypeItem;
use App\Models\QuotationRemark;
use App\Models\RefrigerationSale;
use App\Models\RefrigerationSaleHeaderType;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class QuotationController extends Controller
{
    protected int $post_type_id = 1;
    protected string $title = 'Hygiene';
    protected string $url = 'hygienes';

    public function select2Query(Request $request)
    {
        return Quotation::when($request->has('search'), function ($query) use ($request) {
            $query->where('code', 'LIKE', "%{$request->search}%")
                ->orWhere('poc_name', 'LIKE', "%{$request->search}%");
        })
            ->where('post_type', $this->post_type_id)
            ->limit(10)->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @param $shipment
     * @param Request $request
     * @return Response
     */
    public function index($shipment, Request $request): Response
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

        $quotations = Quotation::with('customer', 'items')
            ->when($search, function ($query) use ($search) {
                $query->where('code', 'LIKE', "%{$search}%");
            })->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })
            ->where('post_type', $this->post_type_id)
            ->where('shipment', RefrigerationSale::SHIPMENT_PARAM[$shipment])
            ->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $quotations->appends($queryString);

        return Inertia::render('Quotation/Index', [
            'shipment' => $shipment,
            'url' => $this->url,
            'title' => $this->title,
            'quotations' => $quotations,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $shipment
     * @return Response
     */
    public function create($shipment): Response
    {
        $item_types = Quotation::TYPE_ARR;
        $code = Quotation::latest()->value('id') ?? 0;

        return Inertia::render('Quotation/Form',
            [
                'shipment' => $shipment,
                'url' => $this->url,
                'title' => $this->title,
                'csrf' => csrf_token(),
                'code' => $code,
                'item_types' => $item_types,
                'status_args' => Quotation::STATUS_SELECT,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $shipment
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    public function store($shipment, Request $request): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required',
            'sub_total' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'gst' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.type' => 'required|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);

        $data = $request->all();
        $data['post_type'] = $this->post_type_id;
        $data['shipment'] = RefrigerationSale::SHIPMENT_PARAM[$shipment];
        $quotation = Quotation::create($data);
        $quotation->update(['code' => str_pad($quotation->id, 4, '0', STR_PAD_LEFT)]);

        foreach ($data['items'] as $item) {
            $item['quotation_id'] = $quotation->id;
            QuotationItem::create($item);
        }

        return redirect("/$shipment/$this->url")->with('created', "Quotation created successfully");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $shipment
     * @param Request $request
     * @param Quotation $quotation
     * @return Application|Redirector|RedirectResponse
     */
    public function update($shipment, Request $request, Quotation $quotation): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required',
            'sub_total' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'gst' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.type' => 'required|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);

        $data = $request->all();
        $quotation->update($data);

        $quotation->items()->delete();

        foreach ($data['items'] as $item) {
            $item['quotation_id'] = $quotation->id;
            QuotationItem::create($item);
        }

        return redirect("/$shipment/$this->url")->with('updated', "Quotation updated successfully");
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param $shipment
     * @param Quotation $quotation
     * @return Response
     */
    public function edit($shipment, Quotation $quotation): Response
    {
        $product_ids = [];
        foreach ($quotation->items as $item) {
            if ($item['product_id']) {
                $product_ids[] = $item['product_id'];
            }
        }

        $customers = self::toSelect2Data(Customer::whereIn('id', [$quotation->customer_id])->get()->toArray(), 'id', 'name');
        $products = self::toSelect2Data(Product::whereIn('id', $product_ids)->get()->toArray(), 'id', 'name');

        $item_types = Quotation::TYPE_ARR;
        $code = Quotation::latest()->value('id') ?? 0;

        return Inertia::render('Quotation/Form', [
            'shipment' => $shipment,
            'url' => $this->url,
            'title' => $this->title,
            'quotation' => $quotation->load('items.product'),
            'csrf' => csrf_token(),
            'code' => $code,
            'item_types' => $item_types,
            'status_args' => Quotation::STATUS_SELECT,

            /** Get exist without ajax **/
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param $shipment
     * @param Quotation $quotation
     * @return Response
     */
    public function show($shipment, Quotation $quotation)
    {
        return Inertia::render('Quotation/Detail', [
            'shipment' => $shipment,
            'url' => $this->url,
            'title' => $this->title,
            'quotation' => $quotation->load('items.product', 'customer'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $shipment
     * @param Request $request
     * @param Quotation $quotation
     * @return RedirectResponse
     */
    public function updatePhoto($shipment, Request $request, Quotation $quotation): RedirectResponse
    {
        /** Update photo **/
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store("quotations/$quotation->id", 'public');
            $quotation->update(['photo' => $path]);
        }

        return back()->with('updated', "Quotation updated successfully");
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param $shipment
     * @param Quotation $quotation
     * @return RedirectResponse
     */
    public function destroy($shipment, Quotation $quotation): RedirectResponse
    {
        $quotation->delete();

        return redirect("/$shipment/$this->url")->with('deleted', "Quotation deleted successfully");
    }
}
