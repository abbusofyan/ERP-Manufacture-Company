<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\RequisitionItem;
use App\Models\Vendor;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderRequisition;
use App\Models\OrderItem;

class RequisitionItemService
{
    public function getConvertingItems(Array $requisitionItemIds) {
        $requisition_items = [];
        $requisition_items = RequisitionItem::whereIn('id', $requisitionItemIds)
        ->with([
            'requisition',
            'product.uom',
            'product.category',
        ])
        ->get()
        ->toArray();
        foreach ($requisition_items as $key => $item) {
            $productVendors = Vendor::with('productPrices')
                ->whereHas('productPrices', function ($query) use ($item) {
                    $query->where('product_id', $item['product_id']);
                    $query->where('vendor_id', $item['product']['vendor1'] ? $item['product']['vendor1']['vendor_id'] : '');
                })
                ->limit(10)
                ->get();
            $requisition_items[$key]['options'] = toSelect2Data($productVendors->toArray(), 'id', 'code', 'name', ' | ');
        }
        return $requisition_items;
    }

    public function buildCreatePayload(Array $data) {
        if ($data['convert_to'] == 'new') {
            return $this->createNewPOPayload($data);
        } else {
            return $this->addToExistingPOPayload($data);
        }
    }

    public function addToExistingPOPayload(Array $data) {
        $payload = [];
        foreach ($data['order_ids'] as $index => $order_id) {
            $order = Order::with('vendor')->find($order_id);
            $product_id = $data['product_ids'][$index];
            $payload[$order_id]['order'] = $order;

            // $orderRequisitions = OrderRequisition::where('order_id', $order_id)->get();
            // foreach ($orderRequisitions as $orderRequisition) {
            //     $orderItem = OrderItem::with('gst', 'product.uom', 'product.category')->find($orderRequisition->order_item_id);
            //     $payload[$order_id]['existing_item'][$orderRequisition->requisition_item_id] = $orderItem;
            // }

            $orderItems = OrderItem::with('gst', 'product.uom', 'product.category')->where('order_id', $order_id)->get();
            foreach ($orderItems as $orderItem) {
                $payload[$order_id]['existing_item'][$orderItem->id] = $orderItem;
            }

            $requisition_item_id = $data['requisition_item_ids'][$index];
            $requisitionItem = RequisitionItem::with([
                'requisition',
                'product.uom',
                'product.category',
                'product.prices' => function ($query) use ($order, $product_id) {
                    $query->where('price', '>', 0)->where('vendor_id', $order->vendor_id)->where('product_id', $product_id)->orderBy('price', 'asc')->take(1);
                },
                'product.prices.vendor'
            ])->find($requisition_item_id);
            $payload[$order_id]['requisition_item'][$requisition_item_id] = $requisitionItem;
        }
        return $payload;
    }

    public function createNewPOPayload(Array $data) {
        $payload = [];
        foreach ($data['vendor_ids'] as $index => $vendor_id) {
            $vendor = Vendor::find($vendor_id);
            $payload[$vendor_id]['vendor'] = $vendor;
            $requisition_item_id = $data['requisition_item_ids'][$index];
            $product_id = $data['product_ids'][$index];
            $requisitionItem = RequisitionItem::with([
                'requisition',
                'product.uom',
                'product.category',
                'product.prices' => function ($query) use ($vendor_id, $product_id) {
                    $query->where('price', '>', 0)->where('vendor_id', $vendor_id)->where('product_id', $product_id)->orderBy('price', 'asc')->take(1);
                },
                'product.prices.vendor'
            ])->find($requisition_item_id);
            $payload[$vendor_id]['requisition_item'][] = $requisitionItem;
        }
        return $payload;
    }
}
