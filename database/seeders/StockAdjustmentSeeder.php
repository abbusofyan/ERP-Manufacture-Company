<?php

namespace Database\Seeders;

use App\Models\StockAdjustment;
use App\Models\StockAdjustmentType;
use App\Models\User;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class StockAdjustmentSeeder extends Seeder
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
        $csv->setHeaderOffset(null);

        // Define expected headers in the CSV file
        $headers = [
            'stock_adjustment_type_code',
            'code',
            'reason',
            'created_by_username',
            'updated_by_username',
            'approved_by_username',
        ];

        // Count total records in CSV
        $records = $csv->getRecords();
        $totalRecords = iterator_count($records);
        $processedRecords = 0;
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $record) {
            $data = [];
            foreach ($headers as $index => $header) {
                $data[$header] = $record[$index] ?? null;
            }

            // Find related data from the database
            $type = StockAdjustmentType::where('code', $data['stock_adjustment_type_code'])->first();
            $createdBy = User::where('username', $data['created_by_username'])->first();
            $updatedBy = User::where('username', $data['updated_by_username'])->first();
            $approvedBy = User::where('username', $data['approved_by_username'])->first();

            if ($type && $createdBy && $updatedBy) {
                // Insert or update StockAdjustment record
                StockAdjustment::updateOrCreate(
                    [
                        'code' => $data['code'],
                    ],
                    [
                        'stock_adjustment_type_id' => $type->id,
                        'reason' => $data['reason'],
                        'created_by' => $createdBy->id,
                        'updated_by' => $updatedBy->id,
                        'approved_by' => $approvedBy ? $approvedBy->id : null,
                    ]
                );

                // Update processed record count and display progress
                $processedRecords++;
                $percentage = round(($processedRecords / $totalRecords) * 100, 2);
                $this->command->info("Progress: {$percentage}% completed ({$processedRecords}/{$totalRecords} records)");
            } else {
                // Display missing data details if any required fields are not found
                $missing = [];
                if (!$type) $missing[] = "Type Code: {$data['stock_adjustment_type_code']}";
                if (!$createdBy) $missing[] = "Created By Username: {$data['created_by_username']}";
                if (!$updatedBy) $missing[] = "Updated By Username: {$data['updated_by_username']}";

                $this->command->error("Missing required data: " . implode(', ', $missing));
            }
        }

        // Display final success message
        $this->command->info("StockAdjustment table seeded successfully with {$processedRecords} records from CSV!");
    }
}
