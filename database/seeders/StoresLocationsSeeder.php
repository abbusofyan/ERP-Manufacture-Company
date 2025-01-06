<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Store;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class StoresLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Path to the CSV file
        $csvPath = database_path('seeders/data_2024nov11/store.csv');

        // Check if the CSV file exists
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        // Create a CSV reader instance
        $csv = Reader::createFromPath($csvPath, 'r');

        // Disable header offset since CSV does not have headers
        $csv->setHeaderOffset(null);

        // Define custom headers based on the CSV column order
        // Assuming the CSV columns are in the following order:
        // 0: id (WarehouseID)
        // 1: name (WarehouseName)
        // 2: status (IsActive)
        $headers = ['id', 'name', 'status'];

        // Iterate through each record in the CSV
        foreach ($csv->getRecords() as $record) {
            // Map CSV columns to the defined headers
            $data = [];
            foreach ($record as $index => $value) {
                if (isset($headers[$index])) {
                    $data[$headers[$index]] = $value;
                }
            }

            // Ensure 'status' is an integer (1 or 0)
            $status = isset($data['status']) && $data['status'] == 1 ? 1 : 0;

            // Create or find the store based on 'id'
            $store = Store::firstOrCreate(
                ['name' => $data['name']],
                [
                    'status' => $status,
                ]
            );

            // Create or find the location associated with the store
            Location::firstOrCreate(
                [
                    'store_id' => $store->id,
                    'code' => $data['id'],
                ],
                [
                    'name' => $data['name'],
                    'status' => $status,
                ]
            );
        }
    }
}
