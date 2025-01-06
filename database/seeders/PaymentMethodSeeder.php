<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use League\Csv\Reader;
use Spatie\Permission\Models\Permission;
use DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $payment_methods = [
            'CASH' => 'CASH',
            'CHEQUE' => 'CHEQUE',
            'E CHEQUE' => 'E CHEQUE',
            'LC' => 'LETTER OF CREDIT',
            'MASTER' => 'MASTER CARD',
            'NETS' => 'NETS',
            'TT' => 'TELEGRAPHIC TRANSFER',
            'VISA' => 'VISA',
            'WIRE' => 'WIRE TRANSFER'
        ];

        foreach ($payment_methods as $code => $method) {
            PaymentMethod::firstOrCreate([
                'code' => $code,
                'name' => $method
            ]);
        }

    }
}
