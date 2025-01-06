<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Vendor;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PaymentMethod;
use App\Models\OrderRequisition;
use App\Models\Terms;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use DB;
use App\Mail\PriceIncreasedNotification;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ConvertRequisitionToPORequest;
use App\Http\Requests\SubmitRequisitionToPORequest;
use App\Http\Requests\SubmitAddToExistingPORequest;
use App\Services\RequisitionItemService;
use App\Services\OrderService;

class RequisitionToPOController extends Controller
{
    public $requisitionItemService;
    public $orderService;

    public function __construct(RequisitionItemService $requisitionItemService, OrderService $orderService)
    {
        $this->requisitionItemService = $requisitionItemService;
        $this->orderService = $orderService;
        // $this->authorizeResource(Order::class, 'order');
    }

    public function index(Request $request): Response
    {
        $search = $request->search;
        $transaction_type = $request->transaction_type;
        $requisitionable_id = $request->requisitionable_id;

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
        $filters['transaction_type'] = $transaction_type;
        $filters['requisitionable_id'] = $requisitionable_id;

        $requisition_items = RequisitionItem::with([
            'product.uom',
            'requisition.order',
            'requisition.requisitionable',
            'requisition.createdBy',
            'checkedBy'
        ])
        ->where('pending_order_qty', '>', 0)
        ->when($search, function ($query) use ($search) {
            $query->where('product_name', 'LIKE', "%{$search}%");
            $query->orWhere('uom', 'LIKE', "%{$search}%");
            $query->orWhereHas('product', function ($query) use ($search) {
                  $query->where('sku', 'LIKE', "%{$search}%");
              });
        })
        ->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })
        ->whereHas('requisition', function ($query) use ($transaction_type, $requisitionable_id) {
            $query->where('status', Requisition::APPROVED_STATUS)
                  ->when($transaction_type !== null, function ($query) use ($transaction_type) {
                      $query->where('type', $transaction_type);
                  })
                  ->when($requisitionable_id !== null, function ($query) use ($requisitionable_id) {
                      $query->where('requisitionable_id', $requisitionable_id);
                  });
        })
        ->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $requisition_items->appends($queryString);
        $transaction_types = Requisition::ORDER_TYPE_ARRAY;

        return Inertia::render('RequisitionToPO/Index', [
            'requisition_items' => $requisition_items,
            'filters' => $filters,
            'csrf' => csrf_token(),
            'transaction_types' => $transaction_types,
        ]);
    }


    public function create(Request $request)
    {
        return Inertia::render($request->convert_to == 'new' ? 'RequisitionToPO/Form' : 'RequisitionToPO/FormExistingPO', [
            'csrf' => csrf_token(),
            'payload' => $this->requisitionItemService->buildCreatePayload($request->all()),
            'payment_methods' => toSelect2Data(PaymentMethod::all()->toArray(), 'id', 'name'),
            'terms' => toSelect2Data(Terms::all()->toArray(), 'id', 'name')
        ]);
    }

    public function convert(ConvertRequisitionToPORequest $request)
    {
        return Inertia::render('RequisitionToPO/Converting', [
            'csrf' => csrf_token(),
            'requisition_items' => $this->requisitionItemService->getConvertingItems($request->items),
        ]);
    }


    public function addToExistingPO(ConvertRequisitionToPORequest $request)
    {
        return Inertia::render('RequisitionToPO/Converting', [
            'csrf' => csrf_token(),
            'requisition_items' => $this->requisitionItemService->getConvertingItems($request->items),
            'orders' => toSelect2Data(
                Order::with('vendor')->where('status', Order::STATUS_PENDING)->get()->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'code' => $order->code,
                        'vendor_name' => $order->vendor->name ?? '',
                    ];
                })->toArray(), 'id', 'code', 'vendor_name', ' | '
            )
        ]);
    }


    public function store(SubmitRequisitionToPORequest $request)
    {
        DB::beginTransaction();

        try {
            $this->orderService->createOrder($request->payload);

            DB::commit();

            return redirect('/requisitions-to-order')->with('created', 'Purchase Order created successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error($e->getMessage());

            return redirect()->back()->with('deleted', $e->getMessage());
        }
    }

    public function submitAddToExistingPO(SubmitAddToExistingPORequest $request)
    {
        DB::beginTransaction();

        try {
            $this->orderService->addOrderItem($request->payload);

            DB::commit();

            return redirect('/requisitions-to-order')->with('created', 'New item(s) added to existing PO');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error($e->getMessage());

            return redirect()->back()->with('deleted', $e->getMessage());
        }
    }


    /*
    Cancel requested item
     */
    public function cancel(Request $request)
    {
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $item_id) {
                $requisition_item = RequisitionItem::find($item_id);

                if ($requisition_item) {
                    $requisition_item->update(['status' => RequisitionItem::REJECTED_STATUS]);
                }
            }
        }

        return redirect('/requisitions-to-order')->with('deleted', 'Requisition canceled successfully');
    }
}
