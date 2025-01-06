<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GST;
use League\Csv\Reader;
use Spatie\Permission\Models\Permission;
use DB;

class GSTSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $gsts = [
            [
                'code' => 'TX9',
                'rate' => 9.0000,
                'description' => 'Taxables Supplier / Trader',
            ],
            [
                'code' => 'ZP',
                'rate' => 0.0000,
                'description' => 'Zero-Rated Purchases',
            ],
            [
                'code' => 'NR',
                'rate' => 0.0000,
                'description' => 'Non GST Registered Supplier / Trader',
            ],
            [
                'code' => 'TX7',
                'rate' => 7.0000,
                'description' => 'Taxables Supplier / Trader',
            ],
            [
                'code' => 'TX8',
                'rate' => 8.0000,
                'description' => 'Taxables Supplier / Trader',
            ],
        ];


        foreach ($gsts as $gst) {
            GST::firstOrCreate([
                'code' => $gst['code'],
                'value' => $gst['rate'],
                'description' => $gst['description']
            ]);
        }

    }
}
