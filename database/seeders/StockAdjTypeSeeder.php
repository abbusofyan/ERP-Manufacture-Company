<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Product;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockAdjustmentType;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use League\Csv\Reader;
use Spatie\Permission\Models\Role;

class StockAdjTypeSeeder extends Seeder
{
    public function run()
    {
        $csv = Reader::createFromPath(database_path('seeders/data_temp/stock_adjustment_type.csv'), 'r');
        $csv->setHeaderOffset(0);

        $count = 0;
        foreach ($csv->getRecords() as $record) {
            if ($count >= 1000) {
                break;
            }

            StockAdjustmentType::create([
                'name' => $record['TypeName'],
                'code' => $record['TypeID'],
            ]);

            $count++;
        }
    }
}
