<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use League\Csv\Reader;
use Spatie\Permission\Models\Permission;
use DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stores_id = [1, 2, 3, 4, 5];
        $data = [
            [
                'code' => 'GB',
                'name' => 'LANE 2 RACK 3',
            ],
            [
                'code' => 'KB',
                'name' => 'LANE 2 RACK 1',
            ],
            [
                'code' => 'ST',
                'name' => 'LANE 2 RACK 2',
            ],
            [
                'code' => 'TL',
                'name' => 'LANE 3 RACK 1',
            ],
            [
                'code' => 'WD',
                'name' => 'LANE 3 RACK 3',
            ],
        ];


        foreach ($stores_id as $store_id) {
            foreach ($data as $row) {
                if (!Location::where('store_id', $store_id)->where('code', $row['code'])->first()) {
                    Location::firstOrCreate([
                        'code' => $row['code'],
                        'store_id' => $store_id,
                        'name' => $row['name'],
                        'status' => 1
                    ]);
                }

            }
        }

    }
}
