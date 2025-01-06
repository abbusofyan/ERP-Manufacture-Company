<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\OrderRequisition;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\ProductPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\RequisitionService;
use App\Services\OrderItemService;
use App\Services\GoodsReceiptService;
use App\Services\ProductService;

class OrderService
{
    public $orderItemService;

    public function __construct(
        OrderItemService $orderItemService,
        GoodsReceiptService $goodsReceiptService,
        RequisitionService $requisitionService,
        ProductService $productService,
    ) {
        $this->orderItemService = $orderItemService;
        $this->goodsReceiptService = $goodsReceiptService;
        $this->requisitionService = $requisitionService;
        $this->productService = $productService;
    }

    public function getOrderDatatable(Order $order) {
        $order->load([
            'vendor',
            'items.product',
            'items.gst',
            'goodsReceipt' => function ($query) {
                $query->where('status', 1);
            }
        ]);
        $data = DB::table('goods_receipt_histories as grh')
            ->join('goods_receipts as gr', 'grh.goods_receipt_id', '=', 'gr.id')
            ->join('orders', 'gr.order_id', '=', 'orders.id')
            ->join('products as p', 'grh.product_id', '=', 'p.id')
            ->join('unit_of_measurements as uom', 'p.uom_id', '=', 'uom.id')
            ->where('orders.id', $order->id)
            ->select(
                'p.sku', 'p.name as product_name',
                'uom.code as uom', 'grh.created_at',
                'gr.code', 'gr.id as goods_receipt_id',
                'grh.quantity', 'grh.notes'
            )
            ->get();
        $order->history = $data;
        return $order;
    }

    public function createOrder(Array $requestData) {
        $increasedPrice = [];
        foreach ($requestData as $data) {
            $data['purchase_date'] = $data['purchase_date'] ? date('Y-m-d', strtotime($data['purchase_date'])) : null;
            $data['delivery_date'] = $data['delivery_date'] ? date('Y-m-d', strtotime($data['delivery_date'])) : null;
            $data['status'] = Order::STATUS_PENDING;
            $order = Order::create($data);

            $createdBy = [];

            foreach ($data['data'] as $item) {
                $item['vendor_id'] = $data['vendor_id'];

                $orderItem = $this->orderItemService->createOrderItem($order, $item);

                $this->linkOrderRequisition($order, $orderItem, $item['requisition_ids'], $item['requisition_item_ids']);

                $this->updateProductPrice($data['vendor_id'], $item);

                array_push($increasedPrice, $this->getIncreasedPrice($data, $item));

            }
        }

        if (count(array_filter($increasedPrice)) > 0) {
            Mail::to(env('PURCHASING_TEAM_EMAIL'))->queue(new PriceIncreasedNotification($productPriceIncreased));
        }

    }

    private function updateProductPrice($vendorId, $data) {
        $vendorProduct = ProductPrice::where('vendor_id', $vendorId)->where('product_id', $data['product_id'])->first();
        if (!$vendorProduct) {
            ProductPrice::create([
                'vendor_id' => $vendorId,
                'product_id' => $data['product_id'],
                'price' => $data['unit_price']
            ]);
        } else {
            $vendorProduct->price = $data['unit_price'];
            $vendorProduct->save();
        }
    }

    private function getIncreasedPrice($data, $item) {
        $productPriceIncreased = [];
        $vendor_id = $data['vendor_id'];
        $product_id = $item['product_id'];
        $vendorProduct = ProductPrice::where('vendor_id', $vendor_id)->where('product_id', $product_id)->first();
        if ($vendorProduct && ($item['unit_price'] > $vendorProduct->price)) {
            array_push($productPriceIncreased, [
                'vendor_name' => $data['vendor']['name'],
                'item_name' => $item['product']['name'],
                'code' => $item['product']['sku'],
                'uom' => $item['product']['uom']['code'],
                'price_before' => $vendorProduct->price,
                'price_after' => $item['unit_price']
            ]);
        }
        return $productPriceIncreased;
    }

    private function linkOrderRequisition($order, $orderItem, $requisitionIds, $requisitionItemIds) {
        foreach ($requisitionIds as $key => $requisitionId) {
            $a = OrderRequisition::create([
                'order_id' => $order->id,
                'order_item_id' => $orderItem->id,
                'requisition_id' => $requisitionId,
                'requisition_item_id' => $requisitionItemIds[$key],
            ]);

            $reqItem = RequisitionItem::find($requisitionItemIds[$key]);
            $reqItem->update([
                'status' => RequisitionItem::PENDING_CONFIRM_ORDER_STATUS
            ]);
        }
    }

    public function updateStatus(Array $data, Order $order) {
        if ($data['status'] == 3) {
            $order->update(['status' => $data['status']]);

            $this->goodsReceiptService->createGoodsReceipt($order);
            $this->requisitionService->updateRequisitionItemOrderQuantity($order);
            $this->productService->updateOrderedQty($order);
            $message = 'Order Approved';
        }

        if ($data['status'] == 5) {
            $this->cancelOrder($order);
            $message = 'Purchase order has been canceled';
        }

        if ($data['status'] == 6) {
            $this->rejectOrder($order);
            $message = 'Order Rejected';
        }

        return $message;
    }

    public function cancelOrder(Order $order)
    {
        $order->load('items');
        $orderStatus = Order::STATUS_CANCELED;
        foreach ($order->items as $item) {
            $orderItemStatus = OrderItem::STATUS_CANCELED;
            if ($item->received_qty > 0) {
                $orderItemStatus = OrderItem::STATUS_COMPLETED;
                if ($orderStatus == Order::STATUS_CANCELED) {
                    $orderStatus = Order::STATUS_COMPLETED;
                }
            }
            $item->update([
                'status' => $orderItemStatus,
            ]);
            $orderRequisitions = OrderRequisition::where('order_id', $item->order_id)->where('order_item_id', $item->id)->get();
            $canceledQty = $item->quantity - $item->received_qty;
            foreach ($orderRequisitions as $key => $orderRequisition) {
                $reqItem = RequisitionItem::find($orderRequisition->requisition_item_id);

                $product = Product::find($reqItem->product_id);
                $qtyToCancel = min($canceledQty, $reqItem->ordered_qty);
                $product->subOrderedQty($qtyToCancel);

                $reqItem->update([
                    'ordered_qty' => 0
                ]);


                $canceledQty -= $qtyToCancel;
            }
        }
        $order->update(['status' => $orderStatus]);
    }

    public function rejectOrder(Order $order)
    {
        $order->update(['status' => Order::STATUS_REJECTED]);
        $order->load('items');
        foreach ($order->items as $item) {
            $orderRequisitions = OrderRequisition::where('order_id', $item->order_id)->where('order_item_id', $item->id)->get();
            foreach ($orderRequisitions as $key => $orderRequisition) {
                $reqItem = RequisitionItem::find($orderRequisition->requisition_item_id);
                $reqItem->update([
                    'status' => RequisitionItem::PENDING_STATUS,
                ]);
            }
        }
    }

    public function cancelBalance($data) {
        DB::beginTransaction();
        try {
            foreach ($data['items'] as $item) {
                if ($item['canceled_qty'] > 0) {
                    $orderItem = OrderItem::find($item['order_item_id']);
                    if ($item['canceled_qty'] == ($orderItem->quantity - $orderItem->received_qty)) {
                        if ($orderItem->received_qty > 0) {
                            $orderItemStatus = OrderItem::STATUS_COMPLETED;
                        }
                        $orderItem->update(['status' => $orderItemStatus]);
                    } else {
                        dd('cancel half');
                        // if ($orderItem->received_qty >= ($orderItem->quantity - $orderItem->canceled_qty)) {
                        //     $orderItem->status = OrderItem::STATUS_CANCELED;
                        //     $orderItem->save();
                        //
                        //     $order = Order::find($item['order_id']);
                        //     $allItemsCanceled = OrderItem::where('order_id', $order->id)
                        //     ->where('status', '!=', OrderItem::STATUS_CANCELED)
                        //     ->doesntExist();
                        //     if ($allItemsCanceled) {
                        //         $order->update(['status' => Order::STATUS_CANCELED]);
                        //     }
                        // } else {
                        //     $orderItem->save();
                        // }
                    }

                    $orderRequisitions = OrderRequisition::where('order_id', $orderItem->order_id)->where('order_item_id', $orderItem->id)->get();

                    foreach ($orderRequisitions as $key => $orderRequisition) {
                        $reqItem = RequisitionItem::find($orderRequisition->requisition_item_id);
                        $reqItem->update([
                            'ordered_qty' => $reqItem->ordered_qty - $item['canceled_qty']
                        ]);

                        $product = Product::find($reqItem->product_id);
                        $product->subOrderedQty($item['canceled_qty']);
                    }

                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->subOrderedQty($item['canceled_qty']);
                    }

                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return back()->with('deleted', $e->getMessage());
        }

    }


    public function itemReceived(GoodsReceipt $goodsReceipt, $data) {
        foreach ($data as $item) {
            if (empty($item['receiving_qty'])) {
                continue;
            }

            $orderItem = OrderItem::with('order')->find($item['order_item_id']);

            $updateData['received_qty'] = $orderItem->received_qty + $item['receiving_qty'];

            if ($updateData['received_qty'] >= $orderItem->quantity) {
                $updateData['status'] = OrderItem::STATUS_COMPLETED;
            } else {
                $updateData['status'] = OrderItem::STATUS_PARTIALLY_RECEIVED;
            }

            $orderItem->update($updateData);

            $allItemsCompleted = OrderItem::where('order_id', $orderItem->order->id)
                ->where('status', '!=', OrderItem::STATUS_COMPLETED)
                ->doesntExist();

            if ($allItemsCompleted) {
                $orderItem->order->update(['status' => Order::STATUS_COMPLETED]);
            }
        }
    }

    public function addOrderItem(array $payload) {
        foreach ($payload as $data) {
            $order = Order::find($data['order']['id']);
            $order->sub_total = $data['order']['sub_total'];
            $order->total = $data['order']['total'];
            $order->save();

            foreach ($data['new_items'] as $newItems) {
                $newItems['vendor_id'] = $data['order']['vendor_id'];

                if ($newItems['order_item_id']) {
                    $orderItem = OrderItem::find($newItems['order_item_id']);
                    $orderItem->quantity += $newItems['qty'];
                    $orderItem->total += $newItems['total_price'];
                    $orderItem->save();
                } else {
                    $newItems['total'] = $newItems['total_price'];
                    $orderItem = $this->orderItemService->createOrderItem($order, $newItems);
                }

                $this->linkOrderRequisition($order, $orderItem, [$newItems['requisition_id']], [$newItems['requisition_item_id']]);

                $this->updateProductPrice($newItems['vendor_id'], $newItems);
                // array_push($increasedPrice, $this->getIncreasedPrice($data, $item));
            }
        }
    }
}
