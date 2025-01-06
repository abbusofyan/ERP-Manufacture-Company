<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StoreProduct;
use App\Models\ProductPhoto;
use App\Models\ProductDocument;
use App\Models\ProductPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductQuantityService
{
    public function updateRequestedQty($productId, $change)
    {
        $product = Product::findOrFail($productId);
        $product->requested_qty += $change;
        $product->save();
    }

    public function updateCommittedQty($productId, $change)
    {
        $product = Product::findOrFail($productId);
        $product->committed_qty += $change;
        $product->save();
    }

    public function updateOrderedQty($productId, $change)
    {
        $product = Product::findOrFail($productId);
        $product->ordered_qty += $change;
        $product->save();
    }

    public function updateReservedQty($productId, $change)
    {
        $product = Product::findOrFail($productId);
        $product->reserved_qty += $change;
        $product->save();
    }

    public function updateStock($productId, $change)
    {
        $product = Product::findOrFail($productId);
        $product->stock += $change;
        $product->save();
    }
}
