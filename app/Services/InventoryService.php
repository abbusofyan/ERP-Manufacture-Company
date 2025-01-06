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

class InventoryService
{
    public function createNewProduct(array $data)
    {
        $product = Product::create($data);

        // StoreProduct::create([
        //     'store_id' => $data['store_id'],
        //     'location_id' => $data['location_id'],
        //     'product_id' => $product->id,
        // ]);

        if (isset($data['prices'])) {
            foreach ($data['prices'] as $price) {
                if ($price['vendor_id']) {
                    $price['product_id'] = $product->id;
                    $price['price'] = $price['price'];
                    ProductPrice::create($price);
                }
            }
        }
        return $product;
    }

    public function uploadProductPhoto($images, $product) {
        foreach ($images as $image) {
            $is_sales = in_array('MARKETING', array_column(auth()->user()->roles->toArray(), 'name')) ?? false;

            $path = 'product/'.$product->id;
            $name = $image->getClientOriginalName();
            $image->move(public_path($path), $name);
            $filePath = "$path/$name";

            ProductPhoto::create([
                'product_id' => $product->id,
                'user_id' => auth()->user()->id,
                'path' => $filePath,
                'is_sales_photo' => $is_sales
            ]);
        }
    }

    public function uploadDocument($docs, $product) {
        foreach ($docs as $document) {
            $path = 'product/'.$product->id.'/documents';
            $name = $document->getClientOriginalName();
            $document->move(public_path($path), $name);
            $filePath = "$path/$name";

            ProductDocument::create([
                'name' => $name,
                'product_id' => $product->id,
                'file_url' => $filePath,
            ]);
        }
    }
}
