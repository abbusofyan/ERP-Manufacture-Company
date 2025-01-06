<?php

namespace Database\Seeders;

use App\Models\GIN;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\UnavailableStream;
use Illuminate\Support\Facades\DB;

class GINSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     * @throws UnavailableStream
     */

    //SELECT
    //gi.[GINNo] AS code,
    //gi.[TransactionNo] AS requisition_id,
    //gid.[ItemID] AS requisition_item_id,
    //gid.[IssueQty] AS quantity,
    //gi.[Remark] AS remark,
    //gi.[IsActive] AS status
    //FROM
    //[HWEECOOL_HJ].[dbo].[GoodsIssue] gi
    //JOIN
    //[HWEECOOL_HJ].[dbo].[GoodsIssueDetail] gid
    //ON
    //gi.[GINNo] = gid.[GINNo]
    //WHERE
    //gi.[IsActive] = 1;
    public function run()
    {
        $csv = Reader::createFromPath(database_path('seeders/data_temp/gins.csv'), 'r');
        $csv->setHeaderOffset(0);

        $count = 0;
        foreach ($csv->getRecords() as $record) {
            if ($count >= 1000) {
                break;
            }

            DB::transaction(function () use (&$count, $record) {
                // Find the requisition by TransactionNo (assuming it's the requisition ID)
                $requisition = Requisition::where('code', $record['requisition_id'])->first();

                // Check if requisition exists and has items
                if ($requisition && $requisition->requisitionItems()->exists()) {
                    // Get a random item from the requisition
                    $requisitionItem = $requisition->requisitionItems()->pluck('id');
                    if ($requisitionItem->isEmpty()) return;

                    $randomId = $requisitionItem->random();

                    // Create a new GIN record
                    GIN::create([
                        'code' => $record['code'],
                        'requisition_id' => $requisition->id,
                        'requisition_item_id' => $randomId,
                        'quantity' => $record['quantity'],
                        'remark' => $record['remark'],
                        'status' => $record['status'] == 1 ? GIN::CONFIRMED_STATUS : GIN::PENDING_STATUS,
                        'created_at' => Carbon::parse($record['created_at']),
                    ]);

                    $count++;
                }
            });
        }
    }
}
