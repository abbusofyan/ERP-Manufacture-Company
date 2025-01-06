<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\UnitOfMeasurement;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov11/products.csv');

        // Check if the CSV file exists
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        // Read the CSV file
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(null);

        // Define the expected headers in the CSV file
        $headers = [
            'type',
            'category_name',
            'sku',
            'name',
            'description',
            'uom_id',
            'stock',
            'reserved_qty',
            'requested_qty',
            'committed_qty',
            'ordered_qty',
            'weight',
            'dimension_l',
            'dimension_w',
            'dimension_h',
            'assembly',
            'case_pack',
            'lead_time',
            'auto_reorder',
            'reorder_level',
            'quantity_to_reorder',
            'remark',
            'selling_price',
            'landed_cost',
            'status',
            'created_by',
            'updated_by',
        ];

        // Get total records count for progress tracking
        $records = $csv->getRecords();
        $totalRecords = iterator_count($records);
        $processedRecords = 0; // Initialize processed count
        $csv->setHeaderOffset(0); // Reset CSV pointer to start

        // Process each record
        foreach ($records as $record) {
            $data = [];
            foreach ($headers as $index => $header) {
                $data[$header] = $record[$index] ?? null;
            }

            // Find related models
            $category = Category::where('prefix', $data['category_name'])->first();
            $uom = UnitOfMeasurement::where('code', $data['uom_id'])->first();

            // Check if required models exist before creating/updating
            if ($category && $uom) {
                Product::updateOrCreate(
                    ['sku' => $data['sku']],
                    [
                        'type' => $data['type'],
                        'category_id' => $category->id,
                        'name' => $data['name'],
                        'description' => $data['description'],
                        'uom_id' => $uom->id,
                        'stock' => $data['stock'] ?: 0,
                        'reserved_qty' => $data['reserved_qty'] ?: 0,
                        'requested_qty' => $data['requested_qty'] ?: 0,
                        'committed_qty' => $data['committed_qty'] ?: 0,
                        'ordered_qty' => $data['ordered_qty'] ?: 0,
                        'weight' => is_numeric($data['weight']) ? $data['weight'] : null,
                        'dimension_l' => is_numeric($data['dimension_l']) ? $data['dimension_l'] : null,
                        'dimension_w' => is_numeric($data['dimension_w']) ? $data['dimension_w'] : null,
                        'dimension_h' => is_numeric($data['dimension_h']) ? $data['dimension_h'] : null,
                        'case_pack' => is_numeric($data['case_pack']) ? $data['case_pack'] : null,
                        'lead_time' => is_numeric($data['lead_time']) ? $data['lead_time'] : null,
                        'auto_reorder' => $data['auto_reorder'] ?: 0,
                        'reorder_level' => is_numeric($data['reorder_level']) ? $data['reorder_level'] : 0,
                        'quantity_to_reorder' => is_numeric($data['quantity_to_reorder']) ? $data['quantity_to_reorder'] : 0,
                        'remark' => $data['remark'],
                        'selling_price' => is_numeric($data['selling_price']) ? $data['selling_price'] : null,
                        'landed_cost' => is_numeric($data['landed_cost']) ? $data['landed_cost'] : null,
                        'status' => $data['status'] ?: 1,
                    ]
                );
                $processedRecords++; // Increment processed count

                // Calculate and display completion percentage
                $percentage = round(($processedRecords / $totalRecords) * 100, 2);
                $this->command->info("Progress: {$percentage}% completed ({$processedRecords}/{$totalRecords} records)");
            } else {
                // Display missing models information
                $missing = [];
                if (!$category) {
                    $missing[] = "Category with name: {$data['category_name']}";
                }
                if (!$uom) {
                    $missing[] = "UOM with code: {$data['uom_id']}";
                }
                $this->command->error("Missing: " . implode(', ', $missing) . " for SKU: {$data['sku']}");
            }
        }

        // Final completion message
        $this->command->info('Product table seeded successfully from CSV!');
    }
}
