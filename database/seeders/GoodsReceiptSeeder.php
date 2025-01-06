<?php

namespace Database\Seeders;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\UnavailableStream;
use Illuminate\Support\Facades\DB;

class GoodsReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     * @throws UnavailableStream
     */
    public function run()
    {
        $csv = Reader::createFromPath(database_path('seeders/data_temp/goods_receipt.csv'), 'r');
        $csv->setHeaderOffset(0);

        $count = 0;
        foreach ($csv->getRecords() as $record) {
            if ($count >= 1000) {
                break;
            }

            DB::transaction(function () use (&$count, $record) {
                $order = Order::where('code', $record['purchase_order_no'])->first();
                if (empty($order)) return;

                // Create a new goods receipt
                $goodsReceipt = GoodsReceipt::create([
                    'order_id' => $order->id,
                    'code' => $record['grn_no'],
                    'requester_name' => json_encode([$record['created_by']]), // Assuming created_by is the requester_name
                    'is_return' => $record['is_return'] ?? 0,
                    'qr_path' => null, // Assuming we don't have qr_path from the CSV
                ]);

                // Get products associated with the order and pick a random one
                $orderProducts = $order->items()->pluck('product_id');
                if ($orderProducts->isEmpty()) return;

                $randomProductId = $orderProducts->random();

                // Create goods receipt items
                GoodsReceiptItem::create([
                    'goods_receipt_id' => $goodsReceipt->id,
                    'product_id' => $randomProductId,
                    'quantity' => $record['order_bal'],
                    'receive_quantity' => $record['receive_qty'],
                    'is_return' => $record['returned_qty'] > 0 ? 1 : 0,
                ]);

                $count++;
            });
        }
    }
}
