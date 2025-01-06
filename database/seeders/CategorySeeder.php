<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use League\Csv\Reader;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv = Reader::createFromPath(database_path('seeders/data_2024nov11/category.csv'), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $record) {
            Category::firstOrCreate([
                'name' => $record['CategoryName'],
            ], [
                'prefix' => $record['CategoryID'],
            ]);
        }
    }
}
