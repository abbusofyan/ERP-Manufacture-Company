<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\VendorPoc;
use App\Models\Country;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class VendorPocSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov11/vendor_poc.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(null);

        $headers = [
            'vendor_code',
            'full_name',
            'first_name',
            'last_name',
            'country_name',
            'phone',
            'email',
            'title',
            'status',
            'is_default',
        ];

        foreach ($csv->getRecords() as $record) {
            $data = [];
            foreach ($record as $index => $value) {
                if (isset($headers[$index])) {
                    $data[$headers[$index]] = $value;
                }
            }

            $vendor = Vendor::where('code', $data['vendor_code'])->first();

            if ($vendor) {
                $status = isset($data['status']) && $data['status'] == 1 ? 1 : 0;
                $is_default = isset($data['is_default']) && $data['is_default'] == 1 ? 1 : 0;

                $names = explode(' ', $data['full_name'], 2);
                $first_name = $names[0];
                $last_name = isset($names[1]) ? $names[1] : '';

                $country = Country::whereRaw('LOWER(name) = ?', [strtolower($data['country_name'])])->first();
                $country_phone_code_id = $country ? $country->id : null;

                VendorPoc::firstOrCreate(
                    [
                        'vendor_id' => $vendor->id,
                        'email' => $data['email'],
                    ],
                    [
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'country_phone_code_id' => $country_phone_code_id,
                        'phone' => $data['phone'],
                        'title' => $data['title'],
                        'status' => $status,
                        'is_default' => $is_default,
                    ]
                );
            }
        }
    }
}
