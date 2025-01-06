<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StorageItem;
use App\Models\Transfer;
use App\Models\TransferItem;
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use League\Csv\Exception;

class TransferItemSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov11/transfer_items.csv');

        // Check if the CSV file exists
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        try {
            // Read the CSV file
            $csv = Reader::createFromPath($csvPath, 'r');
            $csv->setHeaderOffset(null); // No headers in CSV file

            // Define the field order based on CSV structure
            $fields = [
                'transfer_code',   // Column index 0
                'product_code',    // Column index 1
                'quantity',        // Column index 2
                // 'storage_code',   // Column index 3 (if applicable)
                // 'storage_item_code', // Column index 4 (if applicable)
            ];

            // Count total records for progress display
            $records = $csv->getRecords();
            $totalRecords = iterator_count($records);
            $processedRecords = 0;

            // Reset the CSV reader pointer to the start after counting
            $csv->setHeaderOffset(null);

            foreach ($csv->getRecords() as $record) {
                // Ensure the record has at least the expected number of columns
                if (count($record) < count($fields)) {
                    $this->command->error("Skipping record due to insufficient columns: " . json_encode($record));
                    continue;
                }

                // Map each record to the expected fields
                $data = array_combine($fields, $record);

                // Extract relevant fields and check for null values
                $transferCode = $data['transfer_code'] ?? null;
                $productCode = $data['product_code'] ?? null;
                $quantity = $data['quantity'] ?? null;
                // $storageCode = $data['storage_code'] ?? null; // Uncomment if applicable
                // $storageItemCode = $data['storage_item_code'] ?? null; // Uncomment if applicable

                // Skip records with missing mandatory fields
                if (is_null($transferCode) || is_null($productCode) || is_null($quantity)) {
                    $this->command->error("Skipping record due to missing required data: " . json_encode($record));
                    continue;
                }

                // Find related models
                $transfer = Transfer::where('code', $transferCode)->first();
                $product = Product::where('sku', $productCode)->first(); // Adjust 'sku' if necessary
                // $storage = StorageItem::where('code', $storageCode)->first(); // Uncomment if applicable
                // $storageItem = $storageItemCode ? StorageItem::where('code', $storageItemCode)->first() : null; // Uncomment if applicable

                if ($transfer && $product) { // Add storage checks if applicable
                    // Insert or update TransferItem record
                    TransferItem::updateOrCreate(
                        [
                            'transfer_id' => $transfer->id,
                            'product_id' => $product->id,
                            // 'storage_id' => $storage ? $storage->id : null, // Uncomment if applicable
                            // 'storage_item_id' => $storageItem ? $storageItem->id : null, // Uncomment if applicable
                        ],
                        [
                            'quantity' => $quantity,
                            // 'storage_id' => $storage ? $storage->id : null, // Uncomment if applicable
                            // 'storage_item_id' => $storageItem ? $storageItem->id : null, // Uncomment if applicable
                        ]
                    );

                    $processedRecords++;
                    $percentage = round(($processedRecords / $totalRecords) * 100, 2);
                    $this->command->info("Progress: {$percentage}% completed ({$processedRecords}/{$totalRecords} records)");
                } else {
                    // Log missing related data
                    $missing = [];
                    if (!$transfer) $missing[] = "Transfer with code {$transferCode}";
                    if (!$product) $missing[] = "Product with code {$productCode}";
                    // if (!$storage && $storageCode) $missing[] = "Storage with code {$storageCode}"; // Uncomment if applicable
                    // if ($storageCode && !$storageItem) $missing[] = "StorageItem with code {$storageItemCode}"; // Uncomment if applicable
                    $this->command->error("Missing: " . implode(', ', $missing) . " for TransferItem record: " . json_encode($record));
                }
            }

            $this->command->info("TransferItem table seeded successfully with {$processedRecords} records from CSV!");
        } catch (Exception $e) {
            $this->command->error("Error reading CSV file: " . $e->getMessage());
        }
    }
}
