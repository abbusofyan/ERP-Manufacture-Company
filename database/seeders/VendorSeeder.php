<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\Country;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class VendorSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov11/vendor.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(null);

        $headers = [
            'code',
            'country_name',
            'name',
            'country_phone_code_id',
            'phone',
            'uen',
            'account_manager',
            'remark',
            'credit_term',
            'credit_limit',
            'currency',
            'amount_payable',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'status',
        ];

        foreach ($csv->getRecords() as $record) {
            $data = [];
            foreach ($record as $index => $value) {
                if (isset($headers[$index])) {
                    $data[$headers[$index]] = $value;
                }
            }

            $status = isset($data['status']) && $data['status'] == 1 ? 1 : 0;

            $country = Country::whereRaw('LOWER(name) = ?', [strtolower($data['country_name'])])->first();
            $country_id = $country ? $country->id : null;

            Vendor::firstOrCreate(
                ['code' => $data['code']],
                [
                    'country_id' => $country_id,
                    'name' => $data['name'],
                    'country_phone_code_id' => $country_id,
                    'phone' => $data['phone'],
                    'uen' => $data['uen'],
                    'account_manager' => $data['account_manager'],
                    'remark' => $data['remark'],
                    'credit_term' => $data['credit_term'],
                    'credit_limit' => $data['credit_limit'],
                    'amount_payable' => null,
                ]
            );
        }
    }
}
