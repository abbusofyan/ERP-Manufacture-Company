<?php

namespace Database\Seeders;

use App\Models\ItemMovement;
use App\Models\Product;
use App\Models\Location;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class ItemMovementSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov27/item_movements.csv');

        // Check if the CSV file exists
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        // Read the CSV file
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0); // Use the first row as the header

        // Count total rows
        $records = $csv->getRecords();
        $totalRecords = iterator_count($records);
        $csv->setHeaderOffset(0); // Reset the CSV pointer

        // Initialize progress bar
        $this->command->getOutput()->progressStart($totalRecords);

        // Process each row
        foreach ($csv->getRecords() as $record) {
            // Find Product and Location based on CSV data
            $product = Product::where('sku', $record['product_sku'])->first();
            $location = Location::where('code', $record['warehouse'])->first();

            // Check if the required data exists
            if ($product && $location) {
                ItemMovement::create([
                    'product_id' => $product->id,
                    'store_id' => $location->store_id,
                    'movement_type' => $record['movement_type'],
                    'transaction_type' => $this->mapTransactionType($record['TransactionType']),
                    'transaction_code' => !empty($record['TransactionNo']) ? $record['TransactionNo'] : null,
                    'quantity' => (float)$record['quantity'],
                ]);
            } else {
                $missing = [];
                if (!$product) {
                    $missing[] = "Product SKU: {$record['product_sku']}";
                }
                if (!$location) {
                    $missing[] = "Location (Warehouse): {$record['warehouse']}";
                }

                $this->command->error("Missing: " . implode(', ', $missing));
            }

            // Advance the progress bar
            $this->command->getOutput()->progressAdvance();
        }

        // Finish progress bar
        $this->command->getOutput()->progressFinish();
        $this->command->newLine(); // Add a new line after the progress bar

        // Display completion message
        $this->command->info("ItemMovement table seeded successfully with {$totalRecords} records from CSV!");
    }

    /**
     * Map transaction type string from CSV to constant in the ItemMovement model.
     *
     * @param string $transactionType
     * @return int
     */
    private function mapTransactionType(string $transactionType): int
    {
        return match ($transactionType) {
            'Goods Issue Note', 'Goods Issue Nte' => ItemMovement::GIN_TYPE,
            'Goods Receipt' => ItemMovement::GOOD_RECEIPT_TYPE,
            'Goods Return', 'Goods Return (RTS)', 'Goods Return To Store' => ItemMovement::RETURN_TYPE,
            'ItemTransfer', 'Item Transfer' => ItemMovement::INVENTORY_TRANSFER_TYPE,
            'OPENING' => ItemMovement::OPENING_TYPE,
            'Item Adjustment' => ItemMovement::ITEM_ADJ_TYPE,
            'Item Assembly Convert to QtyOnStock' => ItemMovement::ITEM_ASSEMBLY_TYPE,
        };
    }
}
