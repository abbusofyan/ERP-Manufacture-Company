<?php

namespace Database\Seeders;

use App\Models\StockAdjustment;
use App\Models\StockAdjustmentType;
use App\Models\User;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class StockAdjSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov11/stock_adjustments.csv');

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
            'stock_adjustment_type_id', // Column index 1
            'reason',                  // Column index 2
            'created_by',              // Column index 3
            'updated_by',              // Column index 4
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
            $adjustmentCode = $data['code'] ?? null;
            $typeCode = $data['stock_adjustment_type_id'] ?? null;
            $reason = $data['reason'] ?? null;
            $createdByUsername = $data['created_by'] ?? null;
            $updatedByUsername = $data['updated_by'] ?? null;

            // Skip records with missing mandatory fields
            if (is_null($adjustmentCode) || is_null($typeCode) || is_null($createdByUsername)) {
                $this->command->error("Skipping record due to missing required data.");
                continue;
            }

            // Find or create StockAdjustmentType by `stock_adjustment_type_id`
            $type = StockAdjustmentType::firstOrCreate(
                ['code' => $typeCode],
                ['name' => $typeCode] // Using typeCode as name if no name provided
            );

            // Find `created_by` user
            $createdBy = User::where('name', $createdByUsername)->first();

            // Find `updated_by` user (optional)
            $updatedBy = $updatedByUsername ? User::where('name', $updatedByUsername)->first() : null;

            if ($createdBy) {
                // Insert or update StockAdjustment record
                StockAdjustment::updateOrCreate(
                    [
                        'code' => $adjustmentCode,
                    ],
                    [
                        'stock_adjustment_type_id' => $type->id,
                        'reason' => $reason,
                        'created_by' => $createdBy->id,
                        'updated_by' => $updatedBy ? $updatedBy->id : null,
                    ]
                );

                $processedRecords++;
                $percentage = round(($processedRecords / $totalRecords) * 100, 2);
                $this->command->info("Progress: {$percentage}% completed ({$processedRecords}/{$totalRecords} records)");
            } else {
                $this->command->error("Missing created_by user for Username: {$createdByUsername}");
            }
        }

        $this->command->info("StockAdjustment table seeded successfully with {$processedRecords} records from CSV!");
    }
}
