<?php

namespace App\Services;

use App\Models\ProductionOrder;
use App\Models\ProductionOrderProcess;
use App\Models\EngineeringOrder;
use App\Models\ProductionOrderProcessDetail;
use App\Models\SalesOrderECO;
use App\Models\RefrigerationSale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionOrderService
{
    public function createProductionOrder(SalesOrderECO $order)
    {
        DB::beginTransaction();

        try {

            $quotation = RefrigerationSale::with([
                'specs.detail_spec.items' => function ($query) {
                    $query->whereNotNull('assembly_id')->with('assembly.processes.process');
                }
            ])->findOrFail($order->rs_id);

            $hasEngineeringProcess = false;
            foreach ($quotation->specs as $spec) {
                for ($i = 1; $i <= $spec->qty; $i++) {
                    $code = ProductionOrder::generateCode();
                    $productionOrder = ProductionOrder::create([
                        'sales_order_id' => $order->id,
                        'quotation_id' => $order->rs_id,
                        'code' => $code,
                        'category' => 'Quotation',
                        'status' => ProductionOrder::STATUS_PENDING
                    ]);

                    foreach ($spec->detail_spec->items as $vehicleSpecItem) {
                        foreach ($vehicleSpecItem->assembly->processes as $assemblyProcess) {
                            if ($assemblyProcess->process->type == 'Engineering') {
                                $hasEngineeringProcess = true;
                            }

                            $process = ProductionOrderProcess::create([
                                'production_order_id' => $productionOrder->id,
                                'name' => $assemblyProcess->process->name,
                                'department' => $assemblyProcess->process->type,
                                'standard_time_hour' => $assemblyProcess->process->standard_time_hour,
                                'standard_time_minute' => $assemblyProcess->process->standard_time_minute,
                                'manpower' => $assemblyProcess->process->manpower,
                                'status' => ProductionOrderProcess::STATUS_PENDING,
                            ]);

                            foreach ($assemblyProcess->process->detail as $processDetail) {
                                ProductionOrderProcessDetail::create([
                                    'production_order_id' => $productionOrder->id,
                                    'production_order_process_id' => $process->id,
                                    'group_name' => $processDetail->group_name,
                                    'question' => $processDetail->question,
                                    'form_type' => $processDetail->form_type,
                                ]);
                            }
                        }
                    }

                    // Create engineering order if applicable
                    if ($hasEngineeringProcess) {
                        EngineeringOrder::create([
                            'production_order_id' => $productionOrder->id,
                            'status' => EngineeringOrder::STATUS_PENDING
                        ]);
                    }
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to create production order: ' . $e->getMessage());
        }
    }
}
