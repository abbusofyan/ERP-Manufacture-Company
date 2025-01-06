<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Terms;
use League\Csv\Reader;
use Spatie\Permission\Models\Permission;
use DB;

class TermsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $payment_methods = [
            'NET 120' => 'NET DUE ON RECEIPT',
            'NET 15' => 'NET DUE 15 DAYS',
            'NET 150' => 'NET DUE 150 DAYS',
            'NET 180' => 'NET DUE 180 DAYS',
            'NET 30' => 'NET DUE 30 DAYS',
            'NET 45' => 'NET DUE 45 DAYS',
            'NET 60' => 'NET DUE 60 DAYS',
            'NET 7' => 'NET DUE 7 DAYS',
            'NET 90' => 'NET DUE 90 DAYS',
            'NET DUE' => 'NET DUE ON DAYS',

        ];

        foreach ($payment_methods as $code => $method) {
            Terms::firstOrCreate([
                'code' => $code,
                'name' => $method
            ]);
        }

    }
}
