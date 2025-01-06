<?php

namespace App\Services;

use App\Models\Storage as StorageWarehouse;
use App\Models\StorageItem;
use App\Models\Store;
use App\Models\StoreProduct;
use App\Models\Location;
use App\Models\ItemMovement;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StorageService
{
    public function createStorage($goodsReceipt, $data) {
        $goodsReceipt->load('order.vendor');
        $storage = StorageWarehouse::create([
            'grn_number' => $goodsReceipt->code,
            'product_count' => count(array_filter(array_column($data, 'receiving_qty'))),
            'status' => StorageWarehouse::STATUS_COMPLETED
        ]);
        foreach ($data as $item) {
            if ($item['product_id']) {
                StorageItem::create([
                    'storage_id' => $storage->id,
                    'product_id' => $item['product_id'],
                    'vendor_id' => $goodsReceipt->order->vendor_id,
                    'quantity' => $item['receiving_qty'],
                    'cost_price' => $item['total'],
                ]);

                $store = Store::where('name', Store::STORAGE_WAREHOUSE)->first();
                $data = [
                    'store_id' => $store->id,
                    'product_id' => $item['product_id'],
                ];
                $storeProduct = StoreProduct::where($data)->first();
                if ($storeProduct) {
                    $storeProduct->update(['stock' => $storeProduct->stock + $item['receiving_qty']]);
                } else {
                    $data['stock'] = $item['receiving_qty'];
                    $data['location_id'] = Location::where('store_id', $store->id)->first()?->id;
                    $storeProduct = StoreProduct::create($data);
                }
                ItemMovement::create([
                    'product_id' => $item['product_id'],
                    'store_id' => $store->id,
                    'movement_type' => ItemMovement::MOVEMENT_TYPE_PLUS,
                    'transaction_type' => ItemMovement::PURCHASE_ORDER_TYPE,
                    'transaction_id' => $item['order_id'],
                    'quantity' => $item['receiving_qty']
                ]);

                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->addStock($item['receiving_qty'])->subOrderedQty($item['receiving_qty']);
                }
            }
        }
    }
}
