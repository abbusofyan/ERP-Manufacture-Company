<?php

namespace App\Http\Controllers;

use App\Models\EngineeringOrderProcessTimelog;
use App\Models\EngineeringWorkingHour;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderProcess;
use App\Models\ProductionOrderProcessTimelog;
use App\Models\ProductionWorkingHour;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\EngineeringOrder;
use App\Models\EngineeringOrderProcess;
use App\Models\RefrigerationSaleSpecificationItem;
use App\Models\Process;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class EngineeringOrderController extends Controller
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

        $orders = EngineeringOrder::with([
                'productionOrder.quotation'
            ])->when($search, function ($query) use ($search) {
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

        return Inertia::render('EngineeringOrder/Index', [
            'orders' => $orders,
            'filters' => [],
        ]);
    }


    public function create() {
        $products = Product::with('uom')->get();

        return Inertia::render('EngineeringOrder/Form', [
            'products' => $products,
            'csrf' => csrf_token(),
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Vendor $vendor
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
     * @param Vendor $vendor
     * @return Application|Redirector|RedirectResponse
     */
    // public function update(Request $request, Assembly $assembly): Redirector|RedirectResponse|Application
    // {
    //     $request->validate([
    //         'name' => 'required',
    //         'category' => 'required',
    //     ]);
    //
    //     $assembly->update([
    //         'name' => $request->name,
    //         'category' => $request->category
    //     ]);
    //
    //     AssemblyRequirements::where('assembly_id', $assembly->id)->delete();
    //
    //     foreach ($request->requirements as $requirement) {
    //         AssemblyRequirements::create([
    //             'assembly_id' => $assembly->id,
    //             'product_id' => $requirement['product']['id'],
    //             'qty' => $requirement['qty']
    //         ]);
    //     }
    //
    //     return redirect('/assembly')->with('updated', 'Assembly updated successfully');
    // }


    public function store(Request $request) {
        $order = EngineeringOrder::create([
            'type' => $request->type
        ]);
        dd($order);
        return redirect('/engineering-order')->with('updated', 'Engineering Order');
    }


    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Vendor $vendor
     * @return Response
     */
    public function show(Request $request, EngineeringOrder $EngineeringOrder)
    {
        $EngineeringOrder->load([
            'attachments',
            'productionOrder.salesOrder.mineStones',
            'productionOrder.quotation.specs.detail_spec',
            'productionOrder.processes.detail',
            'productionOrder.processTimelogs.user',
            'productionOrder.processTimelogs.productionOrderProcess'
        ]);
        $engineeringOrderItems = RefrigerationSaleSpecificationItem::with('product.uom')->where('quotation_id', $EngineeringOrder->productionOrder->quotation_id)->get();
        $EngineeringOrder->items = $engineeringOrderItems;

        $products = Product::with('uom')->get();

        return Inertia::render('EngineeringOrder/Detail', [
            'products' => $products,
            'order' => $EngineeringOrder,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Vendor $vendor
     * @return RedirectResponse
     */
    public function destroy(Assembly $assembly)
    {
        $assembly->delete();

        return redirect('/assembly')->with('deleted', 'Assembly deleted successfully');
    }

    public function updateItemUsed(Request $request, EngineeringOrder $EngineeringOrder)
    {
        $existingItems = RefrigerationSaleSpecificationItem::where('quotation_id', $EngineeringOrder->quotation_id)->get();
        $submittedItems = $request->input('items');
        $existingItemIds = $existingItems->pluck('product_id')->toArray();
        $submittedItemIds = array_column($submittedItems, 'product_id');
        $toDelete = array_diff($existingItemIds, $submittedItemIds);

        if (!empty($toDelete)) {
            RefrigerationSaleSpecificationItem::where('quotation_id', $EngineeringOrder->quotation_id)->whereIn('product_id', $toDelete)->delete();
        }

        foreach ($submittedItems as $item) {
            if (!in_array($item['product_id'], $existingItemIds)) {
                RefrigerationSaleSpecificationItem::create([
                    'quotation_id' => $EngineeringOrder->quotation_id,
                    'product_id' => $item['product_id'],
                    'planned_qty' => $item['planned_qty'],
                ]);
            }
        }
        return redirect()->back()->with('updated', 'Materials updated');
    }

    public function updateProcess(Request $request, EngineeringOrder $EngineeringOrder)
    {
        $existingProcesses = EngineeringOrderProcess::where('engineering_order_id', $EngineeringOrder->id)->get();
        $submittedProcesses = $request->input('processes');

        $existingProcessIds = $existingProcesses->pluck('process_id')->toArray();
        $submittedProcessIds = array_column($submittedProcesses, 'process_id');
        $toDelete = array_diff($existingProcessIds, $submittedProcessIds);

        if ($toDelete) {
            EngineeringOrderProcess::where('engineering_order_id', $EngineeringOrder->id)->whereIn('process_id', $toDelete)->delete();
        }

        foreach ($submittedProcesses as $process) {
            if (!in_array($process['process_id'], $existingProcessIds)) {
                EngineeringOrderProcess::create([
                    'engineering_order_id' => $EngineeringOrder->id,
                    'process_id' => $process['process_id'],
                ]);
            }
        }
        return redirect()->back()->with('updated', 'Process updated');
    }

    public function startProcess(EngineeringOrder $engineeringOrder, ProductionOrder $productionOrder, ProductionOrderProcess $productionOrderProcess)
    {
        DB::transaction(function () use ($engineeringOrder, $productionOrder, $productionOrderProcess) {
            $productionOrderProcess->update(['started_at' => now(), 'status' => ProductionOrderProcess::STATUS_STARTED]);

            ProductionOrderProcessTimelog::create([
                'user_id' => auth()->user()->id,
                'production_order_process_id' => $productionOrderProcess->id,
                'start_time' => now(),
                'type' => 'start',
            ]);

            if ($productionOrder->status == ProductionOrder::STATUS_PENDING) {
                $productionOrder->update(['start_date' => now(), 'status' => ProductionOrder::STATUS_STARTED]);
            }

            if ($engineeringOrder->status == EngineeringOrder::STATUS_PENDING) {
                $engineeringOrder->update(['start_date' => now(), 'status' => EngineeringOrder::STATUS_STARTED]);
            }
        });

        return redirect()->back()->with('updated', 'Process has started!');
    }

    public function stopProcess(EngineeringOrder $EngineeringOrder, Process $Process)
    {
        DB::transaction(function () use ($EngineeringOrder, $Process) {
            $engineeringOrderProcess = EngineeringOrderProcess::where([
                'engineering_order_id' => $EngineeringOrder->id,
                'process_id' => $Process->id
            ])->firstOrFail();

            $timelog = $engineeringOrderProcess->timelogs()
                ->whereNull('end_time')
                ->latest('start_time')
                ->first();

            $endTime = now();

            if ($timelog) {
                $startTime = Carbon::parse($timelog->start_time);
                $timelog->update(['end_time' => $endTime]);
            } else {
                return redirect()->back()->withErrors(['error' => 'No active timelog found for this process.']);
            }

            // Calculate process time for EngineeringOrderProcess
            $this->calculateProcessTime($engineeringOrderProcess, $startTime, $endTime);

            // Calculate standard time from standard_time_hour and standard_time_minute
            $standard_time_hour = floatval($engineeringOrderProcess->process->standard_time_hour);
            $standard_time_minute = floatval($engineeringOrderProcess->process->standard_time_minute);

            // Convert hours and minutes to total hours
            $standard_time = $standard_time_hour + ($standard_time_minute / 60);

            $total_time = floatval($engineeringOrderProcess->total_time); // Already includes overtime
            $total_time = $total_time == 0 ? 0.1 : $total_time;
            $efficiency = $standard_time != 0 ? round(($standard_time / $total_time) * 100, 2) : 0;

            $engineeringOrderProcess->update([
                'ended_at' => $endTime,
                'efficiency' => $efficiency,
            ]);
        });

        return redirect()->back()->with('updated', 'Process has ended!');
    }

    protected function calculateProcessTime(EngineeringOrderProcess $process, Carbon $startTime, Carbon $endTime)
    {
        $defaultWorkingHour = EngineeringWorkingHour::where('is_default', true)->first();
        $lunchTime = EngineeringWorkingHour::where('is_lunch_time', true)->first();
        $dinnerTime = EngineeringWorkingHour::where('is_dinner_time', true)->first();

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

            $specificWorkingHour = EngineeringWorkingHour::whereDate('specific_date', $date->toDateString())->first();

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

        EngineeringOrderProcessTimelog::where('engineering_order_process_id', $process->id)
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

            EngineeringOrderProcessTimelog::create([
                'engineering_order_process_id' => $process->id,
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
}
