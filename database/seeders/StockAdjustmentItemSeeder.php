<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\Store;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class StockAdjustmentItemSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov11/stock_adjustment_items.csv');

        // Check if the CSV file exists
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        // Read the CSV file
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(null); // No headers in CSV file

        // Define the field order based on CSV structure
        $fields = [
            'code',                    // Column index 0
            'stock_adjustment_code',   // Column index 1
            'product_code',            // Column index 2
            'store_code',              // Column index 3
            'adjustment',              // Column index 4
            'adjustment_qty',          // Column index 5
        ];

        // Count total records for progress display
        $records = $csv->getRecords();
        $totalRecords = iterator_count($records);
        $processedRecords = 0;

        // Reset the CSV reader pointer to the start after counting
        $csv->setHeaderOffset(null);

        foreach ($csv->getRecords() as $record) {
            // Map each record to the expected fields
            $data = array_combine($fields, $record);

            // Extract relevant fields and check for null values
            $itemCode = $data['code'] ?? null;
            $adjustmentCode = $data['stock_adjustment_code'] ?? null;
            $productCode = $data['product_code'] ?? null;
            $storeCode = $data['store_code'] ?? null;
            $adjustment = $data['adjustment'] ?? null;
            $adjustmentQty = $data['adjustment_qty'] ?? null;

            // Skip records with missing mandatory fields
            if (is_null($itemCode) || is_null($adjustmentCode) || is_null($productCode) || is_null($storeCode)) {
                $this->command->error("Skipping record due to missing required data.");
                continue;
            }

            // Find related models
            $stockAdjustment = StockAdjustment::where('code', $adjustmentCode)->first();
            $product = Product::where('sku', $productCode)->first();
            $location = Location::where('code', $storeCode)->first();

            if ($stockAdjustment && $product && $location) {
                // Insert or update StockAdjustmentItem record
                StockAdjustmentItem::updateOrCreate(
                    [
                        'stock_adjustment_id' => $stockAdjustment->id,
                        'product_id' => $product->id,
                        'store_id' => $location->store_id,
                    ],
                    [
                        'adjustment' => $adjustment,
                        'adjustment_qty' => $adjustmentQty,
                    ]
                );

                $processedRecords++;
                $percentage = round(($processedRecords / $totalRecords) * 100, 2);
                $this->command->info("Progress: {$percentage}% completed ({$processedRecords}/{$totalRecords} records)");
            } else {
                // Log missing related data
                $missing = [];
                if (!$stockAdjustment) $missing[] = "StockAdjustment with code {$adjustmentCode}";
                if (!$product) $missing[] = "Product with code {$productCode}";
                if (!$location) $missing[] = "Store with code {$storeCode}";
                $this->command->error("Missing: " . implode(', ', $missing) . " for Adjustment Item code: {$itemCode}");
            }
        }

        $this->command->info("StockAdjustmentItem table seeded successfully with {$processedRecords} records from CSV!");
    }
}
