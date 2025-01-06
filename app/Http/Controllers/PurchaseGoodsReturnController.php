<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseGoodsReturn as GoodsReturn;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\GoodsReceiptHistory;
use App\Services\GoodsReceiptService;
use DB;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseGoodsReturnController extends Controller
{
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

         $statuses = GoodsReturn::STATUS_TEXT_ARRAY;

         $goodsReturn = GoodsReturn::with('goodsReceipt.order.vendor')
             ->when($search, function ($query) use ($search, $statuses) {
                 $query->where('code', 'LIKE', "%{$search}%");
                 $query->orWhereHas('goodsReceipt', function ($subQuery) use ($search) {
                     $subQuery->where('code', 'LIKE', "%{$search}%");
                 });
                 $query->orWhere(function ($subQuery) use ($search, $statuses) {
                     foreach ($statuses as $statusId => $status) {
                         if (stripos($status, $search) !== false) {
                             $subQuery->orWhere('status', $statusId);
                         }
                     }
                 });
             })->when(($order && $by), function ($query) use ($order, $by) {
                 $query->orderBy($order, $by);
             })->paginate($paginate);

         $queryString = [
             'search' => $search,
             'order' => $order,
             'by' => $by,
             'paginate' => $paginate,
         ];

         $goodsReturn->appends($queryString);

         return Inertia::render('PurchaseGoodsReturn/Index', [
             'goodsReturn' => $goodsReturn,
             'filters' => $filters,
         ]);
     }

    public function show(GoodsReturn $purchase_goods_return)
    {
        $purchase_goods_return->load('goodsReceipt.items', 'goodsReceipt.order.vendor', 'createdBy');
        return Inertia::render('PurchaseGoodsReturn/Detail', [
            'goodsReturn' => $purchase_goods_return
        ]);
    }

    public function update(Request $request, GoodsReturn $purchase_goods_return)
    {
        $request->validate(
            [
                'items.*.return_qty' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        preg_match('/items\.(\d+)\.(return_qty)/', $attribute, $matches);
                        $index = $matches[1] ?? null;
                        if ($index !== null && isset($request->items[$index]['return_qty'])) {
                            $qty = $request->items[$index]['return_qty'];
                            $received_qty = $request->items[$index]['receive_quantity'];
                            if ($qty > $received_qty) {
                                $fail(ucwords(str_replace('_', ' ', 'return_qty')) . " cannot exceed its received quantity.");
                            }
                        }
                    },
                ],
            ]
        );

        if (!collect($request->items)->pluck('return_qty')->filter(fn($qty) => $qty > 0)->count()) {
            return back()->withErrors(['items' => 'At least one return quantity must be greater than 0.'])->withInput();
        }

        DB::beginTransaction();

        try {
            $goodsReturn = GoodsReturn::find($request->goods_return_id);
            $createNewGoodsReceipt = false;

            $goodsReceipt = GoodsReceipt::find($request->goods_receipt_id);
            $order = Order::find($goodsReceipt->order_id);

            foreach ($request->items as $item) {
                if ($item['return_qty'] > 0) {
                    if ($item['need_replacement']) {
                        GoodsReceiptItem::where('goods_receipt_id', $goodsReceipt->id)->where('product_id', $item['product_id'])->update(['return_qty' => $item['return_qty']]);

                        $orderItem = OrderItem::where('order_id', $order->id)->where('product_id', $item['product_id'])->first();
                        $newReceivedQty = $orderItem->received_qty - $item['return_qty'];
                        $orderItem->update([
                            'received_qty' => $newReceivedQty,
                            'status' => $newReceivedQty == 0 ? OrderItem::STATUS_PENDING : OrderItem::STATUS_PARTIALLY_RECEIVED
                        ]);

                        $order->update(['status' => Order::STATUS_CONFIRMED]);

                        // chweck if there's an active GRN for this particular order and product ? update that GRN, else create new GRN
                        $activeGRN = GoodsReceipt::where('order_id', $order->id)->where('status', GoodsReceipt::STATUS_PENDING)->first();
                        if ($activeGRN) {
                            $activeGRNItem = GoodsReceiptItem::where('goods_receipt_id', $activeGRN->id)->where('product_id', $orderItem->product_id)->first();
                            if ($activeGRNItem) {
                                $activeGRNItem->update(['quantity' => $activeGRNItem->quantity + $item['return_qty']]);
                            } else {
                                GoodsReceiptItem::create([
                                    'goods_receipt_id' => $activeGRN->id,
                                    'product_id' => $orderItem->product_id,
                                    'product_name' => $orderItem->product_name,
                                    'quantity' => $item['return_qty'],
                                    'status' => GoodsReceiptItem::STATUS_PENDING,
                                    'receive_quantity' => 0
                                ]);
                            }
                        } else {
                            $createNewGoodsReceipt = true;
                        }

                        $product = Product::find($item['product_id']);
                        $product->subStock($item['return_qty']);
                    } else {
                        GoodsReceiptItem::where('goods_receipt_id', $goodsReceipt->id)->where('product_id', $item['product_id'])->update(['return_qty' => $item['return_qty']]);

                        $product = Product::find($item['product_id']);
                        $product->subStock($item['return_qty']);
                    }

                    GoodsReceiptHistory::create([
                        'goods_receipt_id' => $goodsReceipt->id,
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'quantity' => $item['return_qty'],
                        'notes' => 'Item returned to supplier'
                    ]);
                }
            }

            $goodsReturn->update(['status' => GoodsReturn::STATUS_COMPLETED]);

            if ($createNewGoodsReceipt) {
                $goodsReceiptService = new GoodsReceiptService;
                $goodsReceiptService->createGoodsReceipt($order);
            }

            DB::commit();
            return redirect('/purchase-goods-return')->with('created', 'Goods returned successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            dd('Failed to save goods return : '. $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while processing the request.'])->withInput();
        }
    }


}
