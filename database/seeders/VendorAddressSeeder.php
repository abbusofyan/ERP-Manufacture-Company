<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\VendorAddress;
use App\Models\Country;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class VendorAddressSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeders/data_2024nov11/vendor_address.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(null);

        $headers = [
            'vendor_code',
            'address',
            'unit_no',
            'building_name',
            'postal_code',
            'phone',
            'country_name',
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

                $country = Country::whereRaw('LOWER(name) = ?', [strtolower($data['country_name'])])->first();
                $country_id = $country ? $country->id : null;

                VendorAddress::firstOrCreate(
                    [
                        'vendor_id' => $vendor->id,
                        'address' => $data['address'],
                        'unit_no' => $data['unit_no'],
                    ],
                    [
                        'building_name' => $data['building_name'],
                        'postal_code' => $data['postal_code'],
                        'phone' => $data['phone'],
                        'status' => $status,
                        'is_default' => $is_default,
                    ]
                );
            }
        }
    }
}
