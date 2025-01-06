<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\UnitOfMeasurement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class UnitOfMeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv = Reader::createFromPath(database_path('seeders/data_2024nov11/uom.csv'), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $record) {
            UnitOfMeasurement::firstOrCreate([
                'code' => $record['ID'],
            ], [
                'description' => $record['Name'],
            ]);
        }
    }
}
