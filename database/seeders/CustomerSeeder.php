<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Country;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov11/customers.csv');

        // Check if the CSV file exists
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(null);

        // Define headers as per the structure expected by Customer model
        $headers = [
            'info_type',
            'code',
            'country_id',
            'name',
            'country_phone_code_id',
            'phone',
            'uen',
            'account_manager',
            'customer_type',
            'service',
            'remark',
            'refrigeration_sales',
            'project',
            'address_location',
            'address_location_2',
            'address_unit_no',
            'address_building_name',
            'address_postal_code',
            'address_office_number',
            'poc_first_name',
            'poc_last_name',
            'poc_email',
            'poc_country_phone_code_id',
            'poc_phone',
            'poc_title',
            'credit_term',
            'credit_limit',
            'revenue',
        ];

        $records = $csv->getRecords();

        // Count the records for the progress bar
        $recordCount = iterator_count($records);
        $csv->setHeaderOffset(null); // Reset pointer to the start of the CSV after counting

        // Start the progress bar
        $this->command->getOutput()->progressStart($recordCount);

        foreach ($csv->getRecords() as $record) {
            $data = [];
            foreach ($record as $index => $value) {
                if (isset($headers[$index])) {
                    // Check if value is "NULL" (as text) and set it to null
                    $data[$headers[$index]] = strtoupper(trim($value)) === 'NULL' ? null : $value;
                }
            }

            // Attempt to find the country ID based on the country name
            $country = Country::whereRaw('LOWER(name) = ?', [strtolower($data['country_id'])])->first();
            $country_id = $country ? $country->id : null;

            //Payment term
            if ($data['credit_term'] == 'NET DUE') {
                $data['credit_term'] = Customer::CREDIT_TERM_NET_DUE;
            } else if ($data['credit_term'] == 'NET 30') {
                $data['credit_term'] = Customer::CREDIT_TERM_30;
            }

            // Insert or update the Customer record
            Customer::updateOrCreate(
                ['code' => $data['code']],
                [
                    'info_type' => $data['info_type'],
                    'country_id' => $country_id,
                    'name' => $data['name'],
                    'country_phone_code_id' => $country_id,
                    'phone' => $data['phone'],
                    'uen' => $data['uen'],
                    'account_manager' => $data['account_manager'],
                    'customer_type' => $data['customer_type'],
                    'service' => $data['service'],
                    'remark' => $data['remark'],
                    'refrigeration_sales' => $data['refrigeration_sales'],
                    'project' => $data['project'],
                    'address_location' => $data['address_location'],
                    'address_location_2' => $data['address_location_2'],
                    'address_unit_no' => $data['address_unit_no'],
                    'address_building_name' => $data['address_building_name'],
                    'address_postal_code' => $data['address_postal_code'],
                    'address_office_number' => $data['address_office_number'],
                    'poc_first_name' => $data['poc_first_name'],
                    'poc_last_name' => $data['poc_last_name'],
                    'poc_email' => $data['poc_email'],
                    'poc_country_phone_code_id' => $data['poc_country_phone_code_id'],
                    'poc_phone' => $data['poc_phone'],
                    'poc_title' => $data['poc_title'],
                    'credit_term' => $data['credit_term'],
                    'credit_limit' => $data['credit_limit'],
                    'revenue' => $data['revenue'],
                ]
            );

            // Advance the progress bar
            $this->command->getOutput()->progressAdvance();
        }

        // Finish the progress bar
        $this->command->getOutput()->progressFinish();

        $this->command->info("Customer table seeded successfully from CSV!");
    }
}
