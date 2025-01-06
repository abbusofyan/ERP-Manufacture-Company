<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;

class OrderItemService
{
    public function createOrderItem(Order $order, Array $data) {
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'vendor_id' => $data['vendor_id'],
            'product_id' => $data['product_id'],
            'product_name' => $data['product_name'],
            'unit_price' => $data['unit_price'],
            'quantity' => $data['qty'],
            'gst_id' => $data['gst'],
            'total' => $data['total'],
            'status' => OrderItem::STATUS_PENDING
        ]);
        return $orderItem;
    }
}
