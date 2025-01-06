<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\RefrigerationSale;
use App\Models\SalesOrder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class SalesOrderController extends Controller
{
    protected int $post_type_id = 1;
    protected string $title = 'Hygiene';
    protected string $url = 'hygienes';

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

        $orders = SalesOrder::with('quotation.customer.countryPhoneCode')
            ->when($search, function ($query) use ($search) {
                $query->where('code', 'LIKE', "%{$search}%");
            })->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })
            ->where('post_type', $this->post_type_id)
            ->whereHas('quotation', function ($query) use ($shipment) {
                $query->where('shipment', RefrigerationSale::SHIPMENT_PARAM[$shipment]);
            })
            ->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $orders->appends($queryString);

        return Inertia::render('SalesOrder/Index', [
            'title' => $this->title,
            'shipment' => $shipment,
            'orders' => $orders,
            'filters' => $filters,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param $shipment
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    public function create($shipment, Request $request): Application|RedirectResponse|Redirector
    {
        /** Check if first create start **/
        $request->validate([
            'quotation_id' => 'required|exists:quotations,id',
        ]);

        $quotation = Quotation::find($request->quotation_id);
        $sales_order = SalesOrder::firstOrNew(['quotation_id' => $quotation->quotation_id], ['post_type' => $this->post_type_id]);

        if (!$sales_order->exists) {
            $sales_order->quotation_id = $quotation->id;
            $sales_order->save();

            $sales_order->update(['status' => RefrigerationSale::STATUS_VOID]);
        }
        /** Check if first create end **/

        return redirect("/$shipment/sales-order-$this->url/$sales_order->id")->with('created', 'Sale order created successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param $shipment
     * @param SalesOrder $sales_order
     * @return Response
     */
    public function show($shipment, SalesOrder $sales_order): Response
    {
        $sales_order = $sales_order->load('quotation.customer.countryPhoneCode', 'quotation.items.product');
        return Inertia::render('SalesOrder/Detail', [
            'shipment' => $shipment,
            'order' => $sales_order,
            'quotation' => $sales_order->quotation,
        ]);
    }
}
