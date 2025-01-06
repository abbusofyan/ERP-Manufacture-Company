<?php

namespace Database\Seeders;

use App\Models\StorageItem;
use App\Models\Product;
use App\Models\Storage;
use App\Models\Vendor;
use App\Models\Location;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class StorageItemSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov11/storage_items.csv');

        // Check if the CSV file exists
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        // Read the CSV file without header
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(null); // No header in CSV file

        // Define the headers expected in the CSV file
        $headers = [
            'storage_code',
            'product_sku',
            'vendor_code',
            'location_code',
            'quantity',
            'cost_price',
            'status',
        ];

        // Count total records
        $records = $csv->getRecords();
        $totalRecords = iterator_count($records);
        $processedRecords = 0;
        $csv->setHeaderOffset(null); // Reset CSV pointer without header

        // Process each record manually with assigned headers
        foreach ($csv->getRecords() as $record) {
            $data = [];
            foreach ($headers as $index => $header) {
                $data[$header] = $record[$index] ?? null; // Assign value by index, or null if missing
            }

            // Find related models based on codes in CSV data
            $storage = Storage::where('code', $data['storage_code'])->first();
            $product = Product::where('sku', $data['product_sku'])->first();
            $vendor = Vendor::where('code', $data['vendor_code'])->first();
            $location = Location::where('code', $data['location_code'])->first();

            // Check if required models exist before inserting or updating
            if ($storage && $product && $location) {
                StorageItem::updateOrCreate(
                    [
                        'storage_id' => $storage->id,
                        'product_id' => $product->id,
                        'location_id' => $location->id,
                    ],
                    [
                        'vendor_id' => $vendor ? $vendor->id : null,
                        'quantity' => $data['quantity'] ?? 0,
                        'cost_price' => $data['cost_price'] ?? 0,
                        'status' => $data['status'] ?? 0,
                    ]
                );

                $processedRecords++; // Increment processed record count

                // Display completion percentage
                $percentage = round(($processedRecords / $totalRecords) * 100, 2);
                $this->command->info("Progress: {$percentage}% completed ({$processedRecords}/{$totalRecords} records)");
            } else {
                // Identify and display missing models for error logging
                $missing = [];
                if (!$storage) {
                    $missing[] = "Storage Code: {$data['storage_code']}";
                }
                if (!$product) {
                    $missing[] = "Product SKU: {$data['product_sku']}";
                }
                if (!$location) {
                    $missing[] = "Location Code: {$data['location_code']}";
                }

                $this->command->error("Missing: " . implode(', ', $missing));
            }
        }

        // Display final success message with count of processed records
        $this->command->info("StorageItem table seeded successfully with {$processedRecords} records from CSV!");
    }
}
