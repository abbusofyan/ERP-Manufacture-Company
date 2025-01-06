<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Product;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use League\Csv\Reader;
use Spatie\Permission\Models\Role;

class RequisitionSeeder extends Seeder
{
    public function run()
    {
        $csv = Reader::createFromPath(database_path('seeders/data_temp/requisition.csv'), 'r');
        $csv->setHeaderOffset(0);

        $count = 0;
        foreach ($csv->getRecords() as $record) {
            if ($count > 2000) {
                break;
            }

            $user = User::where('username', $record['approved_by'])->first();
            $product = Product::where('sku', $record['product_id'])->first();

            if ($user && $product) {
                $requisition = Requisition::firstOrCreate([
                    'code' => $record['code'],
                ], [

                    'status' => Requisition::APPROVED_STATUS,
                    'type' => $record['type'],
                    'order_id' => null,
                    'approved_by' => $user->id,
                    'requested_by' => null,
                    'urgent_orders' => null,
                    'date' => null,
                    'approval_date' => Carbon::parse($record['approval_date']),
                    'note' => null,
                    'isRelease' => true,
                ]);

                RequisitionItem::firstOrCreate([
                    'requisition_id' => $requisition->id,
                ], [
                    'product_id' => $product->id,
                    'status' => Requisition::APPROVED_STATUS,
                    'non_inventory' => null,
                    'product_name' => $product->name,
                    'quantity' => $record['quantity'],
                    'date' => null,
                    'remark' => null,
                    'file_url' => null,
                    'isRelease' => true,
                ]);

                $count++;
            }
        }
    }
}
