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
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Order;

class ProductService
{
    public $productHistoryService;

    public function __construct(ProductHistoryService $productHistoryService) {
        $this->productHistoryService = $productHistoryService;
    }

    public function updateRequestedQty(Requisition $requisition) {
        if ($requisition->type != Requisition::OTHERS_TYPE) {
            $requisition->load('requisitionItems.product');
            foreach ($requisition->requisitionItems as $item) {
                $item->product->requested_qty += $item->requested_qty;
                $item->product->save();
            }
        }
    }

    public function cancelItemPendingOrder(RequisitionItem $requisitionItem) {
        $requisitionItem->load('product');
        $requisitionItem->product->requested_qty -= $requisitionItem->pending_order_qty;
        $requisitionItem->product->save();
    }

    public function updateOrderedQty(Order $order) {
        $order->load('items.product');
        foreach ($order->items as $item) {
            $product = $item->product;
            $product->ordered_qty += $item->quantity;
            $product->save();
        }
    }

    public function createNewProduct(Array $data)
    {
        $data['created_by'] = auth()->user()->id;

        $product = Product::create($data);

        if (isset($data['prices'])) {
            foreach ($data['prices'] as $price) {
                if ($price['vendor_id']) {
                    $price['product_id'] = $product->id;
                    $price['price'] = $price['price'];
                    ProductPrice::create($price);
                }
            }
        }

        if (isset($data['photos']) && $data['photos']) {
            $this->uploadProductPhoto($data['photos'], $product);
        }

        if (isset($data['documents']) && $data['documents']) {
            $this->uploadDocument($data['documents'], $product);
        }

        return $product;
    }

    public function updateProduct(Product $product, Array $data) {
        $product['updated_by'] = auth()->user()->id;
        $product->update($data);

        if (isset($data['prices'])) {
            $product->prices()->forceDelete();
            foreach ($data['prices'] as $price) {
                $price['product_id'] = $product->id;
                ProductPrice::create($price);
            }
        }

        if (isset($data['photos']) && $data['photos']) {
            $this->uploadProductPhoto($data['photos'], $product);
        }

        if (isset($data['documents']) && $data['documents']) {
            $this->uploadDocument($data['documents'], $product);
        }

        if (isset($data['delete_documents'])) {
            foreach ($data['delete_documents'] as $document) {
                $r = ProductDocument::find($document);
                if ($r) $r->delete();
            }
        }
        return $product;
    }

    public function uploadProductPhoto(Array $images, Product $product) {
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

        $this->productHistoryService->addProductHistory($product, 'uploadProductPhoto');

    }

    public function uploadDocument(Array $docs, Product $product) {
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

        $this->productHistoryService->addProductHistory($product, 'uploadPartBook');
    }
}
