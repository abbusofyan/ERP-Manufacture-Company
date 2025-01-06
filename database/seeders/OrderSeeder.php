<?php

namespace Database\Seeders;

use App\Models\Storage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    //SELECT
    //po.[PurchaseOrderNo] AS code,
    //po.[VendorRefNo] AS ref_no,
    //po.[VendorID] AS vendor_id,
    //pod.[ItemID] AS product_id,
    //po.[ShipToAddress] AS delivery_address,
    //po.[DeliveryDate] AS delivery_date,
    //pod.[UnitPrice] AS price,
    //pod.[OrderQty] AS quantity,
    //pod.[ItemDescription] AS description,
    //po.[CurrencyID] AS currency,
    //po.[TermID] AS credit_term,
    //pod.[TaxAmount] AS tax,
    //po.[DeliveryDate] AS estimated_end_date
    //FROM
    //[HWEECOOL_HJ].[dbo].[PurchaseOrder] po
    //JOIN
    //[HWEECOOL_HJ].[dbo].[PurchaseOrderDetail] pod
    //ON po.[PurchaseOrderNo] = pod.[PurchaseOrderNo]
    //WHERE
    //po.[IsActive] = 1
    //ORDER BY
    //po.[DeliveryDate];

    public function run()
    {
        $csv = Reader::createFromPath(database_path('seeders/data_temp/order.csv'), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $record) {
            DB::transaction(function () use ($record) {
                $vendor = Vendor::where("code", $record['vendor_id'])->first();
                $product = Product::where("sku", $record['product_id'])->first();

                if ($vendor && $product) {
                    $vendorAddress = $vendor->addresses()->where('is_default', true)->first();

                    $order = Order::firstOrCreate([
                        'code' => $record['code'],
                    ], [
                        'status' => 3,
                        'ref_no' => $record['ref_no'],
                        'vendor_id' => $vendor->id,
                        'vendor_address' => $vendorAddress ? $vendorAddress->address : null,
                        'vendor_phone' => $vendorAddress ? $vendorAddress->phone : null,
                        'delivery_address' => $record['delivery_address'],
                        'edd' => Carbon::parse($record['delivery_date']),
                        'remark' => null,
                        'qr_path' => null,
                    ]);

                    OrderItem::firstOrCreate([
                        'order_id' => $order->id,
                    ], [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'date' => Carbon::parse($record['delivery_date']),
                        'price' => $record['price'],
                        'quantity' => $record['quantity'],
                        'description' => $record['description'],
                        'remark' => null,
                        'currency' => $record['currency'],
                        'credit_term' => $record['credit_term'],
                        'tax' => $record['tax'],
                        'gst' => 0,
                        'nr' => null,
                        'freight_charges' => null,
                        'discount' => null,
                        'image_url' => null,
                    ]);
                }
            });
        }
    }
}
