<?php

namespace Database\Seeders;

use App\Models\Storage;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class StorageSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov11/storages.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        // Read the CSV file
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(null);

        // Define the headers expected in the CSV
        $headers = [
            'code',
            'grn_number',
            'product_count',
            'status',
        ];

        foreach ($csv->getRecords() as $record) {
            $data = [];
            foreach ($record as $index => $value) {
                if (isset($headers[$index])) {
                    $data[$headers[$index]] = $value;
                }
            }

            // Create or update the Storage record based on 'code'
            Storage::updateOrCreate(
                ['code' => $data['code']],
                [
                    'grn_number' => $data['grn_number'],
                    'product_count' => $data['product_count'] ?? 0,
                    'status' => $data['status'] ?? Storage::STATUS_DRAFT,
                ]
            );
        }

        $this->command->info('Storage table seeded successfully from CSV!');
    }
}
