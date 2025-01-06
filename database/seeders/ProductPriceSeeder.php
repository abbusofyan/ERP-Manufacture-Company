<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class ProductPriceSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov11/product_prices.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        // Read the CSV file
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(null);

        // Define the headers expected in the CSV
        $headers = [
            'product_id',
            'vendor_id',
            'price',
            'last_cost',
            'lowest_cost',
            'highest_cost',
        ];

        foreach ($csv->getRecords() as $record) {
            $data = [];
            foreach ($record as $index => $value) {
                if (isset($headers[$index])) {
                    $data[$headers[$index]] = $value;
                }
            }

            $product = Product::where('sku', $data['product_id'])->first();
            $vendor = Vendor::where('code', $data['vendor_id'])->first();

            // Check if both product and vendor exist
            if ($product && $vendor) {
                ProductPrice::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'vendor_id' => $vendor->id,
                    ],
                    [
                        'price' => $data['price'] ?? 0,
                        'last_cost' => $data['last_cost'] ?? 0,
                        'lowest_cost' => $data['lowest_cost'] ?? 0,
                        'highest_cost' => $data['highest_cost'] ?? 0,
                    ]
                );
                $this->command->info("Product or Vendor added for Product ID: {$data['product_id']}, Vendor ID: {$data['vendor_id']}");
            } else {
                $this->command->error("Product or Vendor not found for Product ID: {$data['product_id']}, Vendor ID: {$data['vendor_id']}");
            }
        }

        $this->command->info('ProductPrice table seeded successfully from CSV!');
    }
}
