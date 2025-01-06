<?php

namespace Database\Seeders;

use App\Models\Assembly;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class AssemblySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Path to the CSV file
        $csvPath = database_path('seeders/data_2024nov11/assembly.csv');

        // Check if the CSV file exists
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        try {
            // Read the CSV file
            $csv = Reader::createFromPath($csvPath, 'r');
            $csv->setHeaderOffset(null); // No headers in CSV file

            // Define the expected field order
            $expectedFields = ['code', 'name', 'category', 'uom', 'status'];

            // Iterate through each record
            foreach ($csv->getRecords() as $record) {
                // Map CSV columns to model attributes based on expectedFields order
                $assemblyData = [
                    'code' => $record[0] ?? null,
                    'name' => $record[1] ?? null,
                    'category' => $record[2] ?? null,
                    'uom' => $record[3] ?? null,
                    'status' => $record[4] ?? 'Inactive',
                ];

                // Insert or update the Assembly record
                Assembly::updateOrCreate(
                    ['code' => $assemblyData['code']],
                    $assemblyData
                );
            }

            $this->command->info("Assembly table seeded successfully from CSV!");

        } catch (\Exception $e) {
            $this->command->error("Error reading CSV file: " . $e->getMessage());
        }
    }
}
