<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductionOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 100; $i++) {
            DB::table('production_orders')->insert([
                'quotation_id' => rand(1, 50), // Randomize these as needed
                'sales_order_id' => rand(1, 50),
                'code' => 'PO-' . strtoupper(Str::random(8)),
                'category' => ['Standard', 'Custom'][array_rand(['Standard', 'Custom'])],
                'start_date' => Carbon::now()->subDays(rand(1, 30)),
                'completion_date' => Carbon::now()->addDays(rand(1, 30)),
                'status' => rand(0, 1) ? '1' : '0',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
