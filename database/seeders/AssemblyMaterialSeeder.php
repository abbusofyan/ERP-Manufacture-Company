<?php

namespace Database\Seeders;

use App\Models\AssemblyMaterial;
use App\Models\Assembly;
use App\Models\Product;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class AssemblyMaterialSeeder extends Seeder
{
    public function run()
    {
        // Define the path to the CSV file
        $csvPath = database_path('seeders/data_2024nov11/assembly_material.csv');

        // Check if the CSV file exists
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        // Read the CSV file
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(null); // No headers in the CSV

        // Count total records for progress display
        $records = $csv->getRecords();
        $totalRecords = iterator_count($records);

        // Reset the CSV reader pointer to the start after counting
        $csv->setHeaderOffset(null);

        // Start the progress bar
        $this->command->getOutput()->progressStart($totalRecords);

        // Define the expected order of fields in the CSV file
        foreach ($csv->getRecords() as $record) {
            $assemblyCode = $record[0];
            $productSku = $record[1];
            $quantity = $record[2];

            // Find Assembly and Product IDs
            $assembly = Assembly::where('code', $assemblyCode)->first();
            $product = Product::where('sku', $productSku)->first();

            if ($assembly && $product) {
                // Insert into AssemblyMaterial table
                AssemblyMaterial::create([
                    'assembly_id' => $assembly->id,
                    'product_id' => $product->id,
                    'qty' => is_numeric($quantity) ? (float)$quantity : null,
                ]);
            } else {
                $this->command->error("Skipping: Assembly or Product not found for assembly_code: {$assemblyCode}, product_sku: {$productSku}");
            }

            // Advance the progress bar
            $this->command->getOutput()->progressAdvance();
        }

        // Finish the progress bar
        $this->command->getOutput()->progressFinish();

        $this->command->info("AssemblyMaterial seeder completed successfully.");
    }
}
