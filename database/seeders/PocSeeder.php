<?php

namespace Database\Seeders;

use App\Models\Poc;
use App\Models\Country;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class PocSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov11/customer_pocs.csv');

        // Check if the CSV file exists
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(null);

        // Define headers as per the structure expected by Customer model
        $headers = [
            'customer_id',
            'first_name',
            'last_name',
            'country_phone_code_id',
            'phone',
            'email',
            'title',
            'country_fax_code_id',
            'fax',
            'remark',
            'status',
            'is_default',
        ];

        $records = $csv->getRecords();

        // Count the records for the progress bar
        $recordCount = iterator_count($records);
        $csv->setHeaderOffset(null); // Reset pointer to the start of the CSV after counting

        // Start the progress bar
        $this->command->getOutput()->progressStart($recordCount);

        foreach ($records as $record) {
            $data = [];
            foreach ($record as $index => $value) {
                if (isset($headers[$index])) {
                    // Check if the value is "NULL" (as text) and set it to null
                    $data[$headers[$index]] = strtoupper(trim($value)) === 'NULL' ? null : $value;
                }
            }

            // Find the Customer based on 'code' instead of 'id'
            $customer = Customer::where('code', $data['customer_id'])->first();

            if (!$customer) {
                // If customer not found, log a warning and skip this record
                $this->command->warn("Customer with code '{$data['customer_id']}' not found. Skipping...");
                $this->command->getOutput()->progressAdvance();
                continue;
            }

            // Replace 'customer_id' in POC with the actual 'id' of the Customer
            $data['customer_id'] = $customer->id;

            // Handle other fields if necessary (e.g., country_phone_code_id, country_fax_code_id)
            // Assume that country_phone_code_id and country_fax_code_id in CSV are 'code' of the country
            if ($data['country_phone_code_id']) {
                $countryPhone = Country::where('name', $data['country_phone_code_id'])->first();
                $data['country_phone_code_id'] = $countryPhone ? $countryPhone->id : null;
            }

            if ($data['country_fax_code_id']) {
                $countryFax = Country::where('name', $data['country_fax_code_id'])->first();
                $data['country_fax_code_id'] = $countryFax ? $countryFax->id : null;
            }

            // Handle the 'status' field if necessary
            if (is_null($data['status'])) {
                $data['status'] = Poc::STATUS_APPROVE;
            }

            // Create or update the POC
            Poc::updateOrCreate(
                [
                    // Assume that each POC is unique for each customer by email
                    'customer_id' => $data['customer_id'],
                    'email' => $data['email'],
                ],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'country_phone_code_id' => $countryPhone ? $countryPhone->id : null,
                    'phone' => $data['phone'],
                    'title' => $data['title'],
                    'country_fax_code_id' => $countryFax ? $countryFax->id : null,
                    'fax' => $data['fax'],
                    'remark' => $data['remark'],
                    'status' => $data['status'],
                    'is_default' => $data['is_default'] ?? 0,
                ]
            );

            // Advance the progress bar
            $this->command->getOutput()->progressAdvance();
        }

        // Finish the progress bar
        $this->command->getOutput()->progressFinish();

        $this->command->info("Poc table seeded successfully from CSV!");
    }
}
