<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customers')->insert([
            'customer_id' => 'CUS48789',
            'type' => 'organisation',
            'country' => 'Singapore',
            'name' => 'NTUC FAIRPRICE COOPERATIVE LTD',
            'country_code' => 'Country Code 1',
            'office_number' => '+65 1234 5678',
            'office_number_1' => '+65 1234 5678',
            'uen' => '199201209N',
            'account_manager' => 'Manager',
            'customer_type' => 'Distributor',
            'service' => 'Service 1',
            'remark' => 'Remark 1',
            'refrigeration_sales' => 'Refrigeration Sales 1',
            'project' => 'Project 1',
            'address_1' => 'Joo Koon circle',
            'address_2' => 'Address 2',
            'unit_no' => '82',
            'building_name' => 'Jurong Industrial Estate',
            'postal_code' => '629101',
            'first_name' => 'First Name 1',
            'last_name' => 'Last Name 1',
            'country_code_1' => 'Country Code 1',
            'phone_number' => '+65 1234 5678',
            'title' => 'Sale Manager',
            'email' => 'email1@example.com',
            'credit_term' => 'COD',
            'credit_limit' => '2000',
        ]);
    }
}
