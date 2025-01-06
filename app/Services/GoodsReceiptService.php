<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\GoodsReceiptDocument;
use App\Models\GoodsReceiptHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoodsReceiptService
{
    public function createGoodsReceipt(Order $order)
    {
        $order->load('items');
        $goodsReceipt = GoodsReceipt::create([
            'order_id' => $order->id,
            'requester_name' => auth()->user()->name,
            'status' => GoodsReceipt::STATUS_PENDING
        ]);
        foreach ($order->items as $item) {
            $quantity = $item->quantity - $item->received_qty;
            if ($quantity > 0) {
                GoodsReceiptItem::create([
                    'goods_receipt_id' => $goodsReceipt->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity - $item->received_qty,
                    'receive_quantity' => 0,
                    'status' => GoodsReceiptItem::STATUS_PENDING
                ]);
            }
        }
        $goodsReceipt->update(['code' => 'GRN' . str_pad($goodsReceipt->id, 4, '0', STR_PAD_LEFT)]);
        return $goodsReceipt;
    }

    public function cancelBalance($data) {
        DB::beginTransaction();
        try {
            foreach ($data['items'] as $item) {
                if ($item['canceled_qty'] > 0) {
                    $goodsReceipt = GoodsReceipt::find($data['goods_receipt_id']);
                    $goodsReceiptItem = GoodsReceiptItem::find($item['goods_receipt_item_id']);
                    $goodsReceiptItem->quantity = $goodsReceiptItem->quantity - $item['canceled_qty'];
                    if ($goodsReceiptItem->quantity == 0) {
                        $goodsReceiptItem->status = GoodsReceiptItem::STATUS_CANCELED;
                        $goodsReceiptItem->save();

                        $allItemsCompleted = GoodsReceiptItem::where('goods_receipt_id', $goodsReceipt->id)
                                             ->where('status', '!=', GoodsReceiptItem::STATUS_CANCELED)
                                             ->doesntExist();
                        if ($allItemsCompleted) {
                            $goodsReceipt->update(['status' => GoodsReceipt::STATUS_CANCELED]);
                        }
                    } else {
                        $goodsReceiptItem->save();
                    }
                    GoodsReceiptHistory::create([
                        'goods_receipt_id' => $goodsReceipt->id,
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'quantity' => $item['canceled_qty'],
                        'notes' => 'Balance Cancelation'
                    ]);
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('deleted', $e->getMessage());
        }

    }


    public function uploadAttachment($file, $goodsReceipt) {
        $path = 'goods-receipt/'.$goodsReceipt->code;
        $name = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path($path), $name);
        $filePath = "$path/$name";
        $grd = GoodsReceiptDocument::create([
            'goods_receipt_id' => $goodsReceipt->id,
            'file_name' => $name,
            'file_url' => $filePath,
        ]);
        return $grd;
    }


    public function updateGoodsReceiptItems($goodsReceipt, $data) {
        $crateNewGoodsReceipt = false;
        $goodsReceipt->load('order');
        foreach ($data as $item) {
            if (!empty($item['receiving_qty'])) {
                if ($item['receiving_qty'] < $item['balance_qty']) {
                    $crateNewGoodsReceipt = true;
                }
                $goodsReceiptItem = GoodsReceiptItem::find($item['goods_receipt_item_id']);
                $goodsReceiptItem->update([
                    'receive_quantity' => $item['receiving_qty'],
                    'status' => $item['receiving_qty'] === $item['balance_qty']
                    ? GoodsReceiptItem::STATUS_COMPLETED
                    : GoodsReceiptItem::STATUS_PARTIALLY_RECEIVED,
                ]);

                GoodsReceiptHistory::create([
                    'goods_receipt_id' => $goodsReceipt->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['receiving_qty'],
                    'notes' => 'Item Received'
                ]);
            }
        }
        $allItemsCompleted = GoodsReceiptItem::where('goods_receipt_id', $goodsReceipt->id)
            ->where('status', '!=', GoodsReceiptItem::STATUS_COMPLETED)
            ->doesntExist();
        if ($allItemsCompleted) {
            $goodsReceipt->update(['status' => GoodsReceipt::STATUS_COMPLETED]);
        }

        if ($crateNewGoodsReceipt) {
            $this->createGoodsReceipt($goodsReceipt->order);
        } else {
            GoodsReceipt::where('order_id', $goodsReceipt->order_id)->update(['status' => GoodsReceipt::STATUS_COMPLETED]);
        }

    }
}
