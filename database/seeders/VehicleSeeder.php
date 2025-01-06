<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class VehicleSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov11/vehicles.csv');

        // Check if the CSV file exists
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        // Read the CSV file
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(null); // No headers in CSV file

        // Count total records for progress display
        $records = $csv->getRecords();
        $totalRecords = iterator_count($records);

        // Reset the CSV reader pointer to the start after counting
        $csv->setHeaderOffset(null);

        // Start the progress bar
        $this->command->getOutput()->progressStart($totalRecords);

        foreach ($csv->getRecords() as $record) {
            // Treat 'NULL' strings as null
            $record = array_map(function ($value) {
                $value = trim($value);
                return (strtoupper($value) === 'NULL' || $value === '') ? null : $value;
            }, $record);

            // Extract relevant fields and check for null values
            $licensePlate = $record[0] ?? null;
            $customerId = $record[1] ?? null;

            // Skip records with missing mandatory fields
            if (is_null($licensePlate) || is_null($customerId)) {
                $this->command->error("Skipping record due to missing required data: " . json_encode($record));
                $this->command->getOutput()->progressAdvance();
                continue;
            }

            // Find the customer by provided ID
            $customer = Customer::where('code', $customerId)->first();
            $customerId = $customer ? $customer->id : null;

            if ($customer) {
                // Parse 'other_info' to extract additional data
                $otherInfoData = [];
                if (!empty($record[13])) { // 'other_info' field
                    $otherInfoData = $this->parseOtherInfo($record[13]);
                }

                // Fields mapping between 'other_info' keys and record indices
                $otherInfoMappings = [
                    'Vehicle Class' => 10,          // vehicle_class
                    'Unit Model' => 9,              // model
                    'Vehicle Make' => 8,            // vehicle_make
                    'Yr of Registration' => 17,     // warranty_end_date
                    'Year of Registration' => 17,   // warranty_end_date (alternate key)
                    'Box Size' => 2,                // type
                    'Box size' => 2,                // type (alternate key)
                    'IU' => 12,                     // refrigeration_serial_number
                    'IU No' => 12,                  // refrigeration_serial_number (alternate key)
                    // Add more mappings as needed
                ];

                // Fill missing fields from 'other_info'
                foreach ($otherInfoMappings as $key => $recordIndex) {
                    if (isset($otherInfoData[$key])) {
                        if ($recordIndex !== null && empty($record[$recordIndex])) {
                            $record[$recordIndex] = $otherInfoData[$key];
                        }
                        // You can store additional data as needed
                    }
                }

                // Insert or update Vehicle record
                Vehicle::updateOrCreate(
                    [
                        'license_plate' => $licensePlate,
                    ],
                    [
                        'customer_id' => $customerId,
                        'type' => $record[2] ?? '',
                        'end_user' => $record[3] ?? null,
                        'vehicle_voltage' => $record[4] ?? null,
                        'contact_no' => $record[5] ?? null,
                        'chassis_no' => $record[6] ?? null,
                        'chassis_delivery' => $record[7] ?? null,
                        'vehicle_make' => $record[8] ?? null,
                        'model' => $record[9] ?? null,
                        'vehicle_class' => $record[10] ?? null,
                        'type_of_plate' => $record[11] ?? null,
                        'refrigeration_serial_number' => $record[12] ?? null,
                        'other_info' => $record[13] ?? null,
                        'current_mileage' => is_numeric($record[14]) ? $record[14] : null,
                        // Set null if date fields are empty or invalid to avoid SQL errors
                        'mileage_date' => $this->parseDate($record[15] ?? null),
                        'warranty_mileage' => is_numeric($record[16]) ? $record[16] : null,
                        'warranty_end_date' => $this->parseDate($record[17] ?? null),
                    ]
                );
            } else {
                // Log missing related data
                $this->command->error("Missing Customer with ID {$customerId} for Vehicle record: " . json_encode($record));
            }

            // Advance the progress bar
            $this->command->getOutput()->progressAdvance();
        }

        // Finish the progress bar
        $this->command->getOutput()->progressFinish();

        $this->command->info("Vehicle table seeded successfully from CSV with {$totalRecords} records processed!");
    }

    /**
     * Parse the 'other_info' string into key-value pairs.
     *
     * @param string $otherInfo
     * @return array
     */
    private function parseOtherInfo($otherInfo)
    {
        $data = [];

        // List of keywords to extract
        $keywords = [
            'Vehicle Class',
            'Unit Model',
            'Box Size',
            'Box size',
            'IU No',
            'IU',
            'Yr of Registration',
            'Year of Registration',
            'Max Laden Wt',
            'Max Laden',
            'Vehicle Make',
        ];

        // For each keyword, find and extract the corresponding value
        foreach ($keywords as $keyword) {
            // Build a regex pattern to match the keyword and capture the value
            $pattern = '/' . preg_quote($keyword, '/') . '\s*[:]*\s*(.*?)\s*(?=$|';

            // Build a pattern to match any of the keywords as the next keyword
            $nextKeywords = array_diff($keywords, [$keyword]);
            $pattern .= implode('|', array_map(function ($k) {
                return preg_quote($k, '/');
            }, $nextKeywords));

            $pattern .= ')/is';

            if (preg_match($pattern, $otherInfo, $matches)) {
                $value = trim($matches[1]);
                // Treat 'NULL' strings as null
                $value = (strtoupper($value) === 'NULL' || $value === '') ? null : $value;
                $data[$keyword] = $value;
            }
        }

        return $data;
    }

    /**
     * Parse a date string into a Carbon instance or return null if invalid.
     *
     * @param string|null $dateString
     * @return \Carbon\Carbon|null
     */
    private function parseDate($dateString)
    {
        if (empty($dateString) || strtoupper($dateString) === 'NULL') {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($dateString);
        } catch (\Exception $e) {
            return null;
        }
    }
}
