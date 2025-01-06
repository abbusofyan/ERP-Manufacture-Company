<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCode;
use League\Csv\Reader;
use Spatie\Permission\Models\Permission;
use DB;

class ExpenseCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $codes = [
            [
                'code' => '2100DEFAULT',
            ],
            [
                'code' => '6000NON-ST',
            ],
        ];


        foreach ($codes as $code) {
            ExpenseCode::firstOrCreate([
                'code' => $code['code'],
            ]);
        }

    }
}
