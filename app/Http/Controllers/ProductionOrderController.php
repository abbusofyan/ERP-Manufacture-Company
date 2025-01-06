<?php

namespace App\Http\Controllers;

use App\Models\Assembly;
use App\Models\ProductionOrderProcessTimelog;
use App\Models\ProductionWorkingHour;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderProcess;
use App\Models\RefrigerationSaleSpecificationItem;
use App\Models\Process;
use App\Models\ProductionOrderProcessDetail;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class ProductionOrderController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Assembly::class, 'assembly');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $search = $request->search;

        if ($request->order && $request->by) {
            $order = $request->order;
            $by = $request->by;
        } else {
            $order = 'id';
            $by = 'desc';
        }

        if ($request->paginate) {
            $paginate = $request->paginate;
        } else {
            $paginate = 10;
        }

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $orders = ProductionOrder::with(['quotation', 'salesOrder.mineStones'])->when($search, function ($query) use ($search) {
            $query->where('code', 'LIKE', "%{$search}%");
        })
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);
        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];

        $orders->appends($queryString);

        return Inertia::render('ProductionOrder/Index', [
            'orders' => $orders,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Assembly $assembly
     * @return Response
     */
    public function edit(Assembly $assembly): Response
    {
        $assembly->load('requirements.product.uom');
        $products = Product::with('uom')->get();

        return Inertia::render('Assembly/Form', [
            'assembly' => $assembly,
            'products' => $products
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Assembly $assembly
     * @return Application|Redirector|RedirectResponse
     */
    public function update(Request $request, Assembly $assembly): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
        ]);

        $assembly->update([
            'name' => $request->name,
            'category' => $request->category
        ]);

        AssemblyRequirements::where('assembly_id', $assembly->id)->delete();

        foreach ($request->requirements as $requirement) {
            AssemblyRequirements::create([
                'assembly_id' => $assembly->id,
                'product_id' => $requirement['product']['id'],
                'qty' => $requirement['qty']
            ]);
        }

        return redirect('/assembly')->with('updated', 'Assembly updated successfully');
    }


    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param ProductionOrder $ProductionOrder
     * @return Response
     */
    public function show(Request $request, ProductionOrder $ProductionOrder)
    {
        $ProductionOrder->load([
            'attachments',
        ]);
        $productionOrderItems = RefrigerationSaleSpecificationItem::with('product.uom')->where('quotation_id', $ProductionOrder->quotation_id)->get();
        $ProductionOrder->items = $productionOrderItems;

        $products = Product::with('uom')->get();

        return Inertia::render('ProductionOrder/Detail', [
            'products' => $products,
            'order' => $ProductionOrder,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Assembly $assembly
     * @return RedirectResponse
     */
    public function destroy(Assembly $assembly)
    {
        $assembly->delete();

        return redirect('/assembly')->with('deleted', 'Assembly deleted successfully');
    }

    public function updateItemUsed(Request $request, ProductionOrder $ProductionOrder)
    {
        $existingItems = RefrigerationSaleSpecificationItem::where('quotation_id', $ProductionOrder->quotation_id)->get();
        $submittedItems = $request->input('items');
        $existingItemIds = $existingItems->pluck('product_id')->toArray();
        $submittedItemIds = array_column($submittedItems, 'product_id');
        $toDelete = array_diff($existingItemIds, $submittedItemIds);

        if (!empty($toDelete)) {
            RefrigerationSaleSpecificationItem::where('quotation_id', $ProductionOrder->quotation_id)->whereIn('product_id', $toDelete)->delete();
        }

        foreach ($submittedItems as $item) {
            if (!in_array($item['product_id'], $existingItemIds)) {
                RefrigerationSaleSpecificationItem::create([
                    'quotation_id' => $ProductionOrder->quotation_id,
                    'product_id' => $item['product_id'],
                    'planned_qty' => $item['planned_qty'],
                ]);
            }
        }
        return redirect()->back()->with('updated', 'Materials updated');
    }

    public function updateProcess(Request $request, ProductionOrder $ProductionOrder)
    {
        $existingProcesses = ProductionOrderProcess::where('production_order_id', $ProductionOrder->id)->get();
        $submittedProcesses = $request->input('processes');

        $existingProcessIds = $existingProcesses->pluck('id')->toArray();
        $submittedProcessIds = array_column($submittedProcesses, 'process_id');
        $toDelete = array_diff($existingProcessIds, $submittedProcessIds);

        if ($toDelete) {
            ProductionOrderProcess::where('production_order_id', $ProductionOrder->id)->whereIn('id', $toDelete)->delete();
            ProductionOrderProcessDetail::whereIn('production_order_process_id', $toDelete)->delete();
        }

        foreach ($submittedProcesses as $process) {
            if (!$process['process_id']) {
                $newProcess = ProductionOrderProcess::create([
                    'production_order_id' => $ProductionOrder->id,
                    'department' => $process['department'],
                    'name' => $process['name'],
                    'manpower' => $process['manpower'],
                    'standard_time_hour' => $process['standard_time_hour'],
                    'standard_time_minute' => $process['standard_time_minute'],
                    'status' => ProductionOrderProcess::STATUS_PENDING
                ]);
                if ($process['details']) {
                    foreach ($process['details'] as $processDetail) {
                        ProductionOrderProcessDetail::create([
                            'production_order_id' => $ProductionOrder->id,
                            'production_order_process_id' => $newProcess->id,
                            'group_name' => $processDetail['group_name'],
                            'question' => $processDetail['question'],
                            'form_type' => $processDetail['form_type'],
                        ]);
                    }
                }
            }
        }
        return redirect(url('production-order/' . $ProductionOrder->id))->with('updated', 'Process updated');
    }

    public function startProcess(ProductionOrder $ProductionOrder, ProductionOrderProcess $productionOrderProcess)
    {
        DB::transaction(function () use ($ProductionOrder, $productionOrderProcess) {
            $productionOrderProcess->update(['started_at' => now(), 'status' => ProductionOrderProcess::STATUS_STARTED]);

            ProductionOrderProcessTimelog::create([
                'user_id' => auth()->user()->id,
                'production_order_process_id' => $productionOrderProcess->id,
                'start_time' => now(),
                'type' => 'start',
            ]);

            if ($ProductionOrder->status == ProductionOrder::STATUS_PENDING) {
                $ProductionOrder->update(['start_date' => now(), 'status' => ProductionOrder::STATUS_STARTED]);
            }
        });

        return redirect()->back()->with('updated', 'Process has started!');
    }

    public function stopProcess(ProductionOrder $ProductionOrder, ProductionOrderProcess $productionOrderProcess)
    {
        DB::transaction(function () use ($ProductionOrder, $productionOrderProcess) {
            $timelog = $productionOrderProcess->timelogs()
                ->whereNull('end_time')
                ->latest('start_time')
                ->first();

            $endTime = now();

            if ($timelog) {
                $startTime = Carbon::parse($timelog->start_time);
                $timelog->update(['end_time' => $endTime, 'user_id' => auth()->user()->id]);
            } else {
                return redirect()->back()->withErrors(['error' => 'No active timelog found for this process.']);
            }

            // Adjusted logic from ServiceOrderProcess to ProductionOrderProcess
            $this->calculateProcessTime($productionOrderProcess, $startTime, $endTime);

            // Calculate standard time from standard_time_hour and standard_time_minute
            $standard_time_hour = floatval($productionOrderProcess->standard_time_hour);
            $standard_time_minute = floatval($productionOrderProcess->standard_time_minute);

            // Convert hours and minutes to total hours
            $standard_time = $standard_time_hour + ($standard_time_minute / 60);

            $total_time = floatval($productionOrderProcess->total_time); // Already includes overtime
            $total_time = $total_time == 0 ? 0.1 : $total_time;
            $efficiency = $standard_time != 0 ? round(($standard_time / $total_time) * 100, 2) : 0;

            $productionOrderProcess->update([
                'ended_at' => $endTime,
                'efficiency' => $efficiency,
                'status' => ProductionOrderProcess::STATUS_COMPLETED
            ]);

            // if the last process that ended set completed on production order
            $productionOrder = ProductionOrder::with('processes')->findOrFail($productionOrderProcess->production_order_id);
            $allProcessHasCompleted = array_reduce($productionOrder->processes->toArray(), function ($carry, $item) {
                return $carry && !empty($item['ended_at']);
            }, true);
            if ($allProcessHasCompleted) {
                $productionOrder->update(['ended_at' => $endTime, 'status' => ProductionOrder::STATUS_COMPLETED]);
            }
        });

        return redirect()->back()->with('updated', 'Process has ended!');
    }

    protected function calculateProcessTime(ProductionOrderProcess $process, Carbon $startTime, Carbon $endTime)
    {
        $defaultWorkingHour = ProductionWorkingHour::where('is_default', true)->first();
        $lunchTime = ProductionWorkingHour::where('is_lunch_time', true)->first();
        $dinnerTime = ProductionWorkingHour::where('is_dinner_time', true)->first();

        $period = CarbonPeriod::create($startTime->copy()->startOfDay(), $endTime->copy()->endOfDay());

        $segments = [];

        foreach ($period as $date) {
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $currentStart = $startTime->copy()->max($dayStart);
            $currentEnd = $endTime->copy()->min($dayEnd);

            if ($currentStart >= $currentEnd) {
                continue;
            }

            $specificWorkingHour = ProductionWorkingHour::whereDate('specific_date', $date->toDateString())->first();

            if ($specificWorkingHour) {
                $workingStart = $date->copy()->setTimeFromTimeString($specificWorkingHour->start_time);
                $workingEnd = $date->copy()->setTimeFromTimeString($specificWorkingHour->end_time);
            } elseif ($defaultWorkingHour) {
                $workingStart = $date->copy()->setTimeFromTimeString($defaultWorkingHour->start_time);
                $workingEnd = $date->copy()->setTimeFromTimeString($defaultWorkingHour->end_time);
            } else {
                $workingStart = $dayStart;
                $workingEnd = $dayEnd;
            }

            $excludedPeriods = [];

            if ($lunchTime) {
                $lunchStart = $date->copy()->setTimeFromTimeString($lunchTime->start_time);
                $lunchEnd = $date->copy()->setTimeFromTimeString($lunchTime->end_time);
                $excludedPeriods[] = ['start' => $lunchStart, 'end' => $lunchEnd, 'type' => 'lunch'];
            }
            if ($dinnerTime) {
                $dinnerStart = $date->copy()->setTimeFromTimeString($dinnerTime->start_time);
                $dinnerEnd = $date->copy()->setTimeFromTimeString($dinnerTime->end_time);
                $excludedPeriods[] = ['start' => $dinnerStart, 'end' => $dinnerEnd, 'type' => 'dinner'];
            }

            $daySegments = [];

            if ($currentStart < $workingStart) {
                $otStart = $currentStart;
                $otEnd = $currentEnd->min($workingStart);
                if ($otStart < $otEnd) {
                    $daySegments[] = ['start' => $otStart, 'end' => $otEnd, 'type' => 'ot'];
                }
            }

            $workStart = $currentStart->max($workingStart);
            $workEnd = $currentEnd->min($workingEnd);
            if ($workStart < $workEnd) {
                $daySegments[] = ['start' => $workStart, 'end' => $workEnd, 'type' => 'working'];
            }

            if ($currentEnd > $workingEnd) {
                $otStart = $currentStart->max($workingEnd);
                $otEnd = $currentEnd;
                if ($otStart < $otEnd) {
                    $daySegments[] = ['start' => $otStart, 'end' => $otEnd, 'type' => 'ot'];
                }
            }

            $finalSegments = [];

            foreach ($daySegments as $segment) {
                $segmentStart = $segment['start'];
                $segmentEnd = $segment['end'];
                $segmentType = $segment['type'];

                $splitSegments = [['start' => $segmentStart, 'end' => $segmentEnd, 'type' => $segmentType]];

                foreach ($excludedPeriods as $excluded) {
                    $newSplitSegments = [];
                    foreach ($splitSegments as $splitSegment) {
                        $splitStart = $splitSegment['start'];
                        $splitEnd = $splitSegment['end'];

                        if ($splitEnd <= $excluded['start'] || $splitStart >= $excluded['end']) {
                            $newSplitSegments[] = $splitSegment;
                        } else {
                            if ($splitStart < $excluded['start']) {
                                $newSplitSegments[] = [
                                    'start' => $splitStart,
                                    'end' => $excluded['start'],
                                    'type' => $splitSegment['type']
                                ];
                            }
                            if ($splitEnd > $excluded['end']) {
                                $newSplitSegments[] = [
                                    'start' => $excluded['end'],
                                    'end' => $splitEnd,
                                    'type' => $splitSegment['type']
                                ];
                            }
                        }
                    }
                    $splitSegments = $newSplitSegments;
                }

                $finalSegments = array_merge($finalSegments, $splitSegments);
            }

            $segments = array_merge($segments, $finalSegments);
        }

        ProductionOrderProcessTimelog::where('production_order_process_id', $process->id)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->delete();

        foreach ($segments as $segment) {
            $duration = $segment['end']->diffInMinutes($segment['start']) / 60.0;

            ProductionOrderProcessTimelog::create([
                'production_order_process_id' => $process->id,
                'user_id' => auth()->user()->id,
                'start_time' => $segment['start'],
                'end_time' => $segment['end'],
                'type' => $segment['type'],
            ]);
        }

        $total_time = 0;
        $ot_time = 0;

        $timelogs = $process->timelogs()->get();

        foreach ($timelogs as $timelog) {
            $duration = Carbon::parse($timelog->end_time)->diffInMinutes(Carbon::parse($timelog->start_time)) / 60.0;
            $total_time += $duration;

            if ($timelog->type == 'ot') {
                $ot_time += $duration;
            }
        }

        $process->update([
            'total_time' => round($total_time, 2),
            'overtime' => round($ot_time, 2),
        ]);
    }

    public function detailProcess(ProductionOrder $ProductionOrder, Process $Process)
    {
        $Process->load('detail');
        return Inertia::render('ProductionOrder/DetailProcess', [
            'productionOrder' => $ProductionOrder,
            'process' => $Process,
        ]);
    }

    public function submitDetailProcess(Request $request, ProductionOrderProcess $Process)
    {
        foreach ($request->groups as $groupProcess) {
            foreach ($groupProcess['items'] as $item) {
                $productionOrderProcessDetail = ProductionOrderProcessDetail::findOrFail($item['process_detail_id'])->update([
                    'value' => isset($item['value']) ? $item['value'] : NULL,
                ]);
            }
        }
        // return redirect(url('production-order/' . $Process->production_order_id))->with('updated', 'Process updated');
        return redirect()->back()->with('updated', 'Process updated!');
    }

    public function select2Query(Request $request)
    {
        return ProductionOrder::with(['quotation.customer'])
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($query) use ($search) {
                    $query->where('id', 'LIKE', "%{$search}%")
                        ->orWhere('code', 'LIKE', "%{$search}%")
                        ->orWhereHas('quotation.customer', function ($query) use ($search) {
                            $query->where('name', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->limit(10)
            ->get();

    }

    // public function uploadAttachment(Request $request, ProductionOrder $ProductionOrder) {
    //     $request->validate([
    //         'file_url' => 'required|file|mimes:jpg,jpeg,png|max:2048',
    //         'remark' => 'nullable|string|max:255',
    //     ]);
    //     if ($request->hasFile('file_url')) {
    //         $file = $request->file('file_url');
    //         $path = $file->store('attachments', 'public');
    //
    //         ProductionOrderAttachment::create([
    //             'production_order_id' => $ProductionOrder->id,
    //             'name' => $file->getClientOriginalName(),
    //             'file_url' => $path,
    //             'remarks' => $request->input('remarks'),
    //         ]);
    //     }
    //     return redirect()->back()->with('updated', 'File uploaded!');
    // }

    // public function updateAttachment(Request $request, ProductionOrder $ProductionOrder)
    // {
    //     $existingAttachments = ProductionOrderAttachment::where('production_order_id', $ProductionOrder->id)->get();
    //     $submittedAttachments = $request->input('attachments');
    //     $existingAttachmentIds = $existingAttachments->pluck('id')->toArray();
    //     $submittedAttchmentIds = array_column($submittedAttachments, 'process_id');
    //     $toDelete = array_diff($existingProcessIds, $submittedProcessIds);
    //     dd($toDelete);
    //     if ($toDelete) {
    //         ProductionOrderProcess::where('production_order_id', $ProductionOrder->id)->whereIn('process_id', $toDelete)->delete();
    //     }
    //
    //     foreach ($submittedProcesses as $process) {
    //         if (!in_array($process['process_id'], $existingProcessIds)) {
    //             ProductionOrderProcess::create([
    //                 'production_order_id' => $ProductionOrder->id,
    //                 'process_id' => $process['process_id'],
    //             ]);
    //         }
    //     }
    //     return redirect()->back()->with('updated', 'Process updated');
    // }

}
