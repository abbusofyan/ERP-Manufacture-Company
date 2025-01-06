<?php

namespace Database\Seeders;

use App\Models\FinishGoods;
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use League\Csv\Exception;

class FinishGoodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the path to the CSV file
        $csvPath = database_path('seeders/data_2024nov11/finish_goods.csv');

        // Check if the CSV file exists
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        try {
            // Read the CSV file
            $csv = Reader::createFromPath($csvPath, 'r');
            $csv->setHeaderOffset(null); // No headers in the CSV

            // Get all records
            $records = $csv->getRecords();
            $totalRecords = iterator_count($records);

            // Reset the CSV reader pointer to the start after counting
            $csv->setHeaderOffset(null);

            // Start the progress bar
            $this->command->getOutput()->progressStart($totalRecords);

            // Iterate through each record
            foreach ($csv->getRecords() as $record) {
                // Ensure the record has at least 5 columns
                if (count($record) < 5) {
                    $this->command->error("Invalid record format: " . json_encode($record));
                    $this->command->getOutput()->progressAdvance();
                    continue;
                }

                // Map CSV columns to model attributes based on expected order
                $code = trim($record[0]);
                $category = trim($record[1]);
                $name = trim($record[2]);
                $uom = trim($record[3]);
                $status = trim($record[4]);

                // Validate mandatory fields
                if (empty($code) || empty($name)) {
                    $this->command->error("Missing mandatory fields in record: " . json_encode($record));
                    $this->command->getOutput()->progressAdvance();
                    continue;
                }

                // Insert or update the FinishGoods record
                FinishGoods::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $name,
                        'category' => $category,
                        'uom' => $uom,
                        'status' => $status == 'Active' ? 1 : 0,
                    ]
                );

                // Advance the progress bar
                $this->command->getOutput()->progressAdvance();
            }

            // Finish the progress bar
            $this->command->getOutput()->progressFinish();

            $this->command->info("FinishGoods seeder completed successfully with {$totalRecords} records processed.");

        } catch (Exception $e) {
            $this->command->error("Error reading CSV file: " . $e->getMessage());
        }
    }
}
