<?php

namespace Database\Seeders;

use App\Models\StoreProduct;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class StoreProductSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov11/store_products.csv');

        // Check if the CSV file exists
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        // Read the CSV file
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(null); // No headers in CSV file

        // Define the field order based on the CSV structure
        $expectedFields = [
            'store_id',
            'location_code', // Placeholder for location code to be converted to location_id
            'sku',           // SKU of the product, to be converted to product_id
            'stock',
            'reserved_qty',
            'requested_qty',
            'committed_qty',
            'ordered_qty',
        ];

        // Count total records for progress display
        $records = $csv->getRecords();
        $totalRecords = iterator_count($records);

        // Reset the CSV reader pointer to the start after counting
        $csv->setHeaderOffset(null);

        // Start the progress bar
        $this->command->getOutput()->progressStart($totalRecords);

        foreach ($csv->getRecords() as $record) {
            // Map each field in CSV to the StoreProduct model based on expectedFields order
            $storeProductData = [];
            foreach ($expectedFields as $index => $field) {
                $storeProductData[$field] = $record[$index] ?? null;
            }

            // Query the location_id from the Location model using location_code
            $location = Location::where('code', $storeProductData['location_code'])->first();
            if (!$location) {
                $this->command->error("Location with code {$storeProductData['location_code']} not found. Skipping record.");
                $this->command->getOutput()->progressAdvance();
                continue;
            }

            // Query the product_id from the Product model using the SKU
            $product = Product::where('sku', $storeProductData['sku'])->first();
            if (!$product) {
                $this->command->error("Product with SKU {$storeProductData['sku']} not found. Skipping record.");
                $this->command->getOutput()->progressAdvance();
                continue;
            }

            // Insert or update StoreProduct record
            StoreProduct::updateOrCreate(
                [
                    'store_id' => $location->store_id,
                    'product_id' => $product->id, // Use product_id from Product lookup
                ],
                [
                    'location_id' => $location->id,
                    'stock' => $storeProductData['stock'],
                    'reserved_qty' => $storeProductData['reserved_qty'],
                    'requested_qty' => $storeProductData['requested_qty'],
                    'committed_qty' => $storeProductData['committed_qty'],
                    'ordered_qty' => $storeProductData['ordered_qty'],
                ]
            );

            // Advance the progress bar
            $this->command->getOutput()->progressAdvance();
        }

        // Finish the progress bar
        $this->command->getOutput()->progressFinish();

        $this->command->info("StoreProduct table seeded successfully from CSV with {$totalRecords} records processed!");
    }
}
