<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use App\Models\StorageItem;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Store;
use App\Models\Vendor;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\OrderRequisition;
use App\Models\RequisitionItem;
use App\Models\Requisition;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Services\GoodsReceiptService;
use App\Services\RequisitionService;
use App\Services\ProductService;
use App\Http\Requests\CreateOrderRequest;

class OrderController extends Controller
{
    public $orderService;
    public $requisitionService;
    public $goodsReceiptService;
    public $productService;

    public function __construct (
        OrderService $orderService,
        RequisitionService $requisitionService,
        GoodsReceiptService $goodsReceiptService,
        ProductService $productService
    )
    {
        $this->authorizeResource(Order::class, 'order');
        $this->orderService = $orderService;
        $this->requisitionService = $requisitionService;
        $this->goodsReceiptService = $goodsReceiptService;
        $this->productService = $productService;
    }

    public function index(Request $request): Response
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

        $statuses = Order::STATUS_TEXT_ARRAY;

        $orders = Order::with(['vendor', 'goodsReceipt' => function ($query) {
                $query->where('status', 1);
            }])
            ->withCount(['items as total_quantity' => function ($query) {
                $query->select(DB::raw('coalesce(sum(quantity), 0)'));
            }])
            ->when($search, function ($query) use ($search, $statuses) {
                $query->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('sub_total', 'LIKE', "%{$search}%")
                    ->orWhere('total', 'LIKE', "%{$search}%")
                    ->orWhereHas('vendor', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('code', 'LIKE', "%{$search}%");
                    });
                $query->orWhere(function ($subQuery) use ($search, $statuses) {
                    foreach ($statuses as $statusId => $status) {
                        if (stripos($status, $search) !== false) {
                            $subQuery->orWhere('status', $statusId);
                        }
                    }
                });
            })
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })
            ->paginate($paginate);


        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $orders->appends($queryString);

        return Inertia::render('Order/Index', [
            'orders' => $orders,
            'filters' => $filters,
        ]);
    }


    public function show(Order $order): Response
    {
        return Inertia::render('Order/Detail', [
            'order' => $this->orderService->getOrderDatatable($order)
        ]);
    }


    public function updateStatus(Request $request, Order $order)
    {
        DB::beginTransaction();

        try {

            $this->orderService->updateStatus($request->all(), $order);

            DB::commit();

            if ($request->status == 3) {
                $message = 'Order Approved';
            }

            if ($request->status == 5) {
                $message = 'Purchase order has been canceled';
            }

            if ($request->status == 6) {
                $message = 'Order Rejected';
            }

            return redirect('/orders')->with('updated', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            dd('error : ' . $e->getMessage());
            return redirect('/orders')->withErrors('Failed to update order status. Please try again.');
        }
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect('/orders')->with('deleted', 'Order deleted successfully');
    }

    private function generateWeekTitle(Carbon $startDate, Carbon $endDate)
    {
        // If same month and same year
        if ($startDate->month === $endDate->month && $startDate->year === $endDate->year) {
            return $startDate->format('M j - ') . $endDate->format('j, Y');
        } else {
            // If different month and same year
            if ($startDate->month !== $endDate->month && $startDate->year === $endDate->year) {
                return $startDate->format('M j') . $endDate->format(' - M j, Y');
            }
        }

        // If different year
        return $startDate->format('M j') . $endDate->format(' - M j, Y');
    }


    /**
     * Display the specified resource from storage.
     *
     * @param Request $request
     * @return Response|void
     */
    public function incoming(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:month,week,list',
        ], [
            'type.in' => 'Invalid value for the type field. Please select a valid type.',
        ]);

        $type = $request->type ?? 'month';


        // Week Listing
        if ($type == 'week') {
            // Week Listing
            $now = Carbon::now();
            $startDate = $request->filled('startDate') ? Carbon::parse($request->startDate) : $now->startOfWeek();

            // Ensure endWeek is 7 days ahead of startWeek
            $endDate = $startDate->copy()->addDays(6);

            $title = $this->generateWeekTitle($startDate, $endDate);

            $orders = Order::with('vendor', 'items.product')
                ->whereBetween('edd', [$startDate, $endDate])
                ->get();

            return Inertia::render('Order/Incoming/Week', [
                'orders' => $orders,
                'title' => $title,
                'startDate' => $startDate->toDateString(),
                'endDate' => $endDate->toDateString(),
            ]);
        } else {
            $request->validate([
                'month' => 'nullable|integer|between:1,12',
                'year' => 'nullable|integer',
            ]);

            $year = $request->filled('year') ? $request->year : now()->year;
            $month = $request->filled('month') ? $request->month : now()->month;
            $title = Carbon::createFromDate($year, $month, 1)->format('F Y');

            $orders = Order::with('vendor', 'items.product')
                ->whereYear('edd', $year)
                ->whereMonth('edd', $month)
                ->get();

            if ($type == 'month') {
                return Inertia::render('Order/Incoming/Month', [
                    'orders' => $orders,
                    'title' => $title,
                    'month' => $month,
                    'year' => $year,
                ]);
            } else {
                return Inertia::render('Order/Incoming/List', [
                    'orders' => $orders,
                    'title' => $title,
                    'month' => $month,
                    'year' => $year,
                ]);
            }
        }
    }


    /**
     * Display the specified resource.
     *
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function pdf(Order $order)
    {
        $items = $order->items()->with('gst')->get();
        $data = [
            'title' => 'PURCHASE ORDER',
            'order' => $order->load('vendor'),
            'items' => $items,
        ];

        $pdf = Pdf::loadView('pdf.purchase_order', $data);
        return $pdf->stream();
    }

    public function getItem(Order $order) {
        return OrderItem::where('order_id', $order->id)->get();
    }

    public function generateGoodsReceipt(Order $order) {
        $goodsReceiptService = new GoodsReceiptService;
        $goodsReceipt = $goodsReceiptService->createGoodsReceipt($order);
        return redirect('/goods-receipts/' . $goodsReceipt->id)->with('created', 'New goods receipt generated successfully');
    }
}
