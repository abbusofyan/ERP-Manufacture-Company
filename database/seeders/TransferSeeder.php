<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Store;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class TransferSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov11/transfer.csv');

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
            'origin_store_code',       // Column index 1
            'destination_store_code',  // Column index 2
            'author_username',         // Column index 3
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
            $code = $data['code'] ?? null;
            $originStoreCode = $data['origin_store_code'] ?? null;
            $destinationStoreCode = $data['destination_store_code'] ?? null;
            $authorUsername = $data['author_username'] ?? null;

            // Skip records with missing mandatory fields
            if (is_null($code) || is_null($originStoreCode) || is_null($destinationStoreCode) || is_null($authorUsername)) {
                $this->command->error("Skipping record due to missing required data.");
                continue;
            }

            // Find related models
            $originStore = Location::where('code', $originStoreCode)->first();
            $destinationStore = Location::where('code', $destinationStoreCode)->first();
            $author = User::where('name', $authorUsername)->first();

            if ($originStore && $destinationStore && $author) {
                // Insert or update Transfer record
                Transfer::updateOrCreate(
                    [
                        'code' => $code,
                    ],
                    [
                        'origin_store_id' => $originStore->store_id,
                        'destination_store_id' => $destinationStore->store_id,
                        'author' => $author->id,
                    ]
                );

                $processedRecords++;
                $percentage = round(($processedRecords / $totalRecords) * 100, 2);
                $this->command->info("Progress: {$percentage}% completed ({$processedRecords}/{$totalRecords} records)");
            } else {
                // Log missing related data
                $missing = [];
                if (!$originStore) $missing[] = "Origin Store with code {$originStoreCode}";
                if (!$destinationStore) $missing[] = "Destination Store with code {$destinationStoreCode}";
                if (!$author) $missing[] = "Author with username {$authorUsername}";
                $this->command->error("Missing: " . implode(', ', $missing) . " for Transfer code: {$code}");
            }
        }

        $this->command->info("Transfer table seeded successfully with {$processedRecords} records from CSV!");
    }
}
