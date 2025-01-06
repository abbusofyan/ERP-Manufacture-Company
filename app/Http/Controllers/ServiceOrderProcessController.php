<?php

namespace App\Http\Controllers;

use App\Models\ServiceAppointment;
use App\Models\ServiceMaintain;
use App\Models\ServiceOrder;
use App\Models\ServiceOrderProcess;
use App\Models\ServiceOrderProcessTimelog;
use App\Models\ServiceWorkingHour;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class ServiceOrderProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ServiceOrder $service_order
     * @param Request $request
     * @return Response
     */
    public function index(ServiceOrder $service_order, Request $request): Response
    {
        // Retrieve search keyword from request
        $search = $request->search;

        // Determine order and sort direction
        $order = $request->order ?? 'id';
        $by = $request->by ?? 'desc';

        // Determine pagination limit
        $paginate = $request->paginate ?? 10;

        // Filters to pass back to the view
        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        // Query to retrieve service orders with related entities
        $processes = ServiceOrderProcess::where('service_order_id', $service_order->id)
            ->with('timelogs', 'technician')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        // Append query string parameters for pagination
        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];
        $processes->appends($queryString);

        // Return view with service order requirements and filters
        return Inertia::render('ServiceOrder/Process/View', [
            'csrf' => csrf_token(),
            'stages' => ServiceOrderProcess::STAGES,
            'types' => ServiceAppointment::TYPES,
            'serviceOrder' => $service_order->load(['serviceQuotation', 'technician', 'customer', 'vehicle.salesOrders.refrigerationSale', 'currentProcess']),
            'filters' => $filters,
            'processes' => $processes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param ServiceOrder $service_order
     * @return RedirectResponse
     */
    public function store(Request $request, ServiceOrder $service_order)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'manpower' => 'nullable|numeric',
            'standard_time' => 'nullable|numeric',
        ]);

        // Create a new service order process
        ServiceOrderProcess::create([
            'service_order_id' => $service_order->id,
            'name' => $request->input('name'),
            'manpower' => $request->input('manpower', 1),
            'standard_time' => $request->input('standard_time', 1),
            'total_time' => 0,
            'overtime' => null,
            'efficiency' => null,
            'status' => ServiceOrderProcess::STATUS_PENDING,
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Service order process created successfully.');
    }

    public function storeFirst(Request $request, ServiceOrder $service_order): RedirectResponse
    {
        // Validate the request
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
        ]);

        $userId = $request->input('user_id');

        if ($userId) {
            // Check if the technician is already assigned to an active process
            $activeProcess = ServiceOrderProcess::where('technician_id', $userId)
                ->whereIn('status', [ServiceOrderProcess::STATUS_IN_PROGRESS, ServiceOrderProcess::STATUS_PAUSE])
                ->first();

            if ($activeProcess) {
                return redirect()->back()->withErrors([
                    'user_id' => 'The selected technician is already assigned to another active process.',
                ]);
            }
        }

        // Create a new service order process
        $process = ServiceOrderProcess::create([
            'stage' => $service_order->stage ?: 1,
            'service_order_id' => $service_order->id,
            'technician_id' => $request->input('user_id', null),
            'begin_datetime' => Carbon::now(),
            'name' => ServiceOrderProcess::STAGES[$service_order->stage ?: 1]['title'],
            'manpower' => 1,
            'standard_time' => 1,
            'total_time' => 0,
            'overtime' => 0,
            'efficiency' => null,
            'status' => ServiceOrderProcess::STATUS_IN_PROGRESS,
        ]);

        // Create an initial timelog with only start_time
        $process->timelogs()->create([
            'start_time' => now(),
        ]);

        // Update the service order
        $service_order->update([
            'status' => ServiceOrder::STATUS_IN_PROGRESS,
            'technician_id' => $request->input('user_id'),
            'stage' => $service_order->stage ?: 1,
            'current_service_order_process_id' => $process->id,
        ]);

        // Update the maintain status
        $service_order->maintain()->update([
            'status' => ServiceMaintain::STATUS_IN_SERVICE,
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Service order process created successfully.');
    }

    public function continue(Request $request, ServiceOrder $service_order, ServiceOrderProcess $process): RedirectResponse
    {
        // Define active statuses
        $activeStatuses = [
            ServiceOrderProcess::STATUS_IN_PROGRESS,
            ServiceOrderProcess::STATUS_PAUSE
        ];

        // Check the current status of the process
        if (in_array($process->status, [ServiceOrderProcess::STATUS_PENDING])) {
            // Process is pending; require 'user_id' to assign a technician
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $userId = $request->input('user_id');

            // Check if the technician is already assigned to another active process
            $activeProcess = ServiceOrderProcess::where('technician_id', $userId)->whereIn('status', $activeStatuses)->first();

            if ($activeProcess) {
                return redirect()->back()->withErrors([
                    'user_id' => 'The selected technician is already assigned to another active process.',
                ]);
            }

            // Assign the technician and update the process status to in progress
            $process->update([
                'technician_id' => $userId,
                'begin_datetime' => Carbon::now(),
                'status' => ServiceOrderProcess::STATUS_IN_PROGRESS,
            ]);

            // Create a new timelog with only start_time
            $process->timelogs()->create([
                'start_time' => now(),
            ]);

            // Update the service order
            $service_order->update([
                'status' => ServiceOrder::STATUS_IN_PROGRESS,
                'technician_id' => $userId,
                'current_service_order_process_id' => $process->id,
            ]);

            // Update the maintain status
            $service_order->maintain()->update([
                'status' => ServiceMaintain::STATUS_IN_SERVICE,
            ]);

            // Redirect back with success message
            return redirect()->back()->with('updated', 'Service order process started successfully.');
        } elseif (in_array($process->status, $activeStatuses)) {
            // Process is already active; proceed with continuation

            $technicianId = $process->technician_id;

            if ($technicianId) {
                // Check if the technician is assigned to another active process excluding the current one
                $activeProcess = ServiceOrderProcess::active()
                    ->where('technician_id', $technicianId)
                    ->where('id', '!=', $process->id)
                    ->first();

                if ($activeProcess) {
                    return redirect()->back()->withErrors([
                        'technician_id' => 'The technician is already assigned to another active process.',
                    ]);
                }
            }

            // Update the process status and begin_datetime
            $process->update([
                'begin_datetime' => Carbon::now(),
                'status' => ServiceOrderProcess::STATUS_IN_PROGRESS,
            ]);

            // Create a new timelog with only start_time
            $process->timelogs()->create([
                'start_time' => now(),
            ]);

            if (is_null($service_order->current_service_order_process_id)) {
                $service_order->update([
                    'status' => ServiceOrder::STATUS_IN_PROGRESS,
                    'current_service_order_process_id' => $process->id,
                ]);

                $service_order->maintain()->update([
                    'status' => ServiceMaintain::STATUS_IN_SERVICE,
                ]);
            }

            // Redirect back with success message
            return redirect()->back()->with('updated', 'Service order process continued successfully.');
        } else {
            // Process is neither pending nor active; cannot continue
            return redirect()->back()->withErrors(['error' => 'This process cannot be continued.']);
        }
    }

    protected function calculateProcessTime(ServiceOrderProcess $process, Carbon $startTime, Carbon $endTime, string $status)
    {
        // Retrieve default working hours, lunch time, and dinner time
        $defaultWorkingHour = ServiceWorkingHour::where('is_default', true)->first();
        $lunchTime = ServiceWorkingHour::where('is_lunch_time', true)->first();
        $dinnerTime = ServiceWorkingHour::where('is_dinner_time', true)->first();

        // Generate periods for each day between startTime and endTime
        $period = CarbonPeriod::create($startTime->copy()->startOfDay(), $endTime->copy()->endOfDay());

        $segments = [];

        foreach ($period as $date) {
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            // Determine the overlap between the process time and the current day
            $currentStart = $startTime->copy()->max($dayStart);
            $currentEnd = $endTime->copy()->min($dayEnd);

            if ($currentStart >= $currentEnd) {
                // No overlap with this day
                continue;
            }

            // Check for specific working hours for this date
            $specificWorkingHour = ServiceWorkingHour::whereDate('specific_date', $date->toDateString())->first();

            // Use specific working hours if available, otherwise use default working hours
            if ($specificWorkingHour) {
                $workingStart = $date->copy()->setTimeFromTimeString($specificWorkingHour->start_time);
                $workingEnd = $date->copy()->setTimeFromTimeString($specificWorkingHour->end_time);
            } elseif ($defaultWorkingHour) {
                $workingStart = $date->copy()->setTimeFromTimeString($defaultWorkingHour->start_time);
                $workingEnd = $date->copy()->setTimeFromTimeString($defaultWorkingHour->end_time);
            } else {
                // If no default working hours, assume the entire day is working time
                $workingStart = $dayStart;
                $workingEnd = $dayEnd;
            }

            // Define lunch and dinner periods for the day
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

            // Before working hours (Overtime)
            if ($currentStart < $workingStart) {
                $otStart = $currentStart;
                $otEnd = $currentEnd->min($workingStart);
                if ($otStart < $otEnd) {
                    $daySegments[] = ['start' => $otStart, 'end' => $otEnd, 'type' => 'ot'];
                }
            }

            // Working hours
            $workStart = $currentStart->max($workingStart);
            $workEnd = $currentEnd->min($workingEnd);
            if ($workStart < $workEnd) {
                $daySegments[] = ['start' => $workStart, 'end' => $workEnd, 'type' => 'working'];
            }

            // After working hours (Overtime)
            if ($currentEnd > $workingEnd) {
                $otStart = $currentStart->max($workingEnd);
                $otEnd = $currentEnd;
                if ($otStart < $otEnd) {
                    $daySegments[] = ['start' => $otStart, 'end' => $otEnd, 'type' => 'ot'];
                }
            }

            // Exclude lunch and dinner times from working and overtime periods
            $finalSegments = [];

            foreach ($daySegments as $segment) {
                $segmentStart = $segment['start'];
                $segmentEnd = $segment['end'];
                $segmentType = $segment['type'];

                // Split the segment further based on excluded periods (lunch and dinner)
                $splitSegments = [['start' => $segmentStart, 'end' => $segmentEnd, 'type' => $segmentType]];

                foreach ($excludedPeriods as $excluded) {
                    $newSplitSegments = [];
                    foreach ($splitSegments as $splitSegment) {
                        $splitStart = $splitSegment['start'];
                        $splitEnd = $splitSegment['end'];

                        if ($splitEnd <= $excluded['start'] || $splitStart >= $excluded['end']) {
                            // No overlap
                            $newSplitSegments[] = $splitSegment;
                        } else {
                            if ($splitStart < $excluded['start']) {
                                // Before excluded period
                                $newSplitSegments[] = [
                                    'start' => $splitStart,
                                    'end' => $excluded['start'],
                                    'type' => $splitSegment['type']
                                ];
                            }
                            // Exclude the lunch/dinner period
                            if ($splitEnd > $excluded['end']) {
                                // After excluded period
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

        // Initialize total_time and overtime
        $total_time = 0;
        $ot_time = 0;

        // Remove existing timelogs that overlap with the time range
        ServiceOrderProcessTimelog::where('service_order_process_id', $process->id)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->delete();

        // Process each segment to calculate durations and create timelogs
        foreach ($segments as $segment) {
            // Duration in hours
            $duration = $segment['end']->diffInMinutes($segment['start']) / 60.0;

            // Create timelog for the segment
            ServiceOrderProcessTimelog::create([
                'service_order_process_id' => $process->id,
                'start_time' => $segment['start'],
                'end_time' => $segment['end'],
                'type' => $segment['type'],
            ]);
        }

        // Sum up durations from all timelogs
        $total_time = 0;
        $ot_time = 0;

        $timelogs = $process->timelogs()->get();

        foreach ($timelogs as $timelog) {
            $duration = Carbon::parse($timelog->end_time)->diffInMinutes(Carbon::parse($timelog->start_time)) / 60.0;

            // Sum all durations for total_time
            $total_time += $duration;

            // Sum overtime separately if needed
            if ($timelog->type == 'ot') {
                $ot_time += $duration;
            }
        }

        // Update the process with accumulated total_time and overtime
        $process->update([
            'begin_datetime' => null,
            'total_time' => round($total_time, 2), // Includes both working hours and overtime
            'overtime' => round($ot_time, 2),
            'status' => $status,
        ]);
    }

    public function pause(Request $request, ServiceOrder $service_order, ServiceOrderProcess $process): RedirectResponse
    {
        // Retrieve the last timelog with null end_time
        $log = ServiceOrderProcessTimelog::where('service_order_process_id', $process->id)
            ->whereNull('end_time')
            ->latest()
            ->first();

        if ($log) {
            $startTime = Carbon::parse($log->start_time);
            $endTime = Carbon::now();
            $log->update(['end_time' => $endTime]);
        } else {
            return redirect()->back()->withErrors(['error' => 'No active timelog found for this process.']);
        }

        // Calculate process time and update the process
        $this->calculateProcessTime($process, $startTime, $endTime, ServiceOrderProcess::STATUS_PAUSE);

        return redirect()->back()->with('updated', 'Service order process paused successfully.');
    }

    public function completed(Request $request, ServiceOrder $service_order, ServiceOrderProcess $process): RedirectResponse
    {
        // Check if the current process matches
        if ($service_order->currentProcess && $service_order->currentProcess->id === $process->id) {
            $service_order_data = [
                'stage' => intval($service_order->stage) == 4 ? intval($service_order->stage) : (intval($service_order->stage) + 1),
                'current_service_order_process_id' => null,
            ];

            if ($service_order->stage == 1) {
                $request->validate([
                    'delivery_date' => 'required|date',
                ]);

                // Check for first check attachment
                $hasFirstCheckAttachment = $service_order->attachments()
                    ->where('stage', 1)
                    ->exists();

                if (!$hasFirstCheckAttachment) {
                    return redirect()->back()->withErrors(['first_attachment' => 'First check attachment is required.']);
                }

                // Assign delivery_date and status
                $service_order_data['delivery_date'] = Carbon::parse($request->input('delivery_date'));
                $service_order_data['status'] = ServiceOrder::STATUS_IN_PROGRESS;
            } else if ($service_order->stage == 2) {
                $service_order_data['status'] = ServiceOrder::STATUS_LAST_CHECK;
            } else if ($service_order->stage == 3) {
                $service_order_data['status'] = ServiceOrder::STATUS_COLLECTION;
            }

            // Update the service order data
            $service_order->update($service_order_data);
        }

        // Load the current process
        $service_order->load('currentProcess');

        // Retrieve the last timelog with null end_time
        $log = ServiceOrderProcessTimelog::where('service_order_process_id', $process->id)
            ->whereNull('end_time')
            ->latest()
            ->first();

        if ($log) {
            $startTime = Carbon::parse($log->start_time);
            $endTime = Carbon::now();
            $log->update(['end_time' => $endTime]);
        } else {
            // Handle case where no open timelog is found
            return redirect()->back()->withErrors(['error' => 'No active timelog found for this process.']);
        }

        // Calculate process time and update the process
        $this->calculateProcessTime($process, $startTime, $endTime, ServiceOrderProcess::STATUS_COMPLETE);

        // Calculate efficiency
        $standard_time = floatval($process->standard_time);
        $total_time = floatval($process->total_time); // Already includes overtime
        if ($total_time == 0) {
            $total_time = 0.1; // Set to 0.1 if total_time is 0
        }
        $efficiency = $standard_time != 0 ? round(($standard_time / $total_time) * 100, 2) : 0;

        // Update efficiency
        $process->update([
            'efficiency' => $efficiency,
        ]);

        // Redirect back with success message
        return redirect()->back()->with('updated', 'Service order process completed successfully.');
    }


    /**
     * Update a resource in storage.
     *
     * @param Request $request
     * @param ServiceOrder $service_order
     * @param ServiceOrderProcess $process
     */
    public function setStandardTime(Request $request, ServiceOrder $service_order, ServiceOrderProcess $process)
    {
        // Validate the request
        $request->validate([
            'standard_time' => 'required|numeric|min:1',
        ]);

        $process->update(['standard_time' => $request->standard_time]);

        // Redirect back with success message
        return redirect()->back()->with('updated', 'Service order process updated successfully.');
    }


    /**
     * Update a resource in storage.
     *
     * @param Request $request
     * @param ServiceOrder $service_order
     * @return RedirectResponse
     */
    public function collected(Request $request, ServiceOrder $service_order): RedirectResponse
    {
        $process = ServiceOrderProcess::create([
            'stage' => ServiceOrderProcess::STAGE_COLLECTED,
            'service_order_id' => $service_order->id,
            'begin_datetime' => Carbon::now(),
            'name' => ServiceOrderProcess::STATUS[ServiceOrderProcess::STAGE_COLLECTED],
            'manpower' => 1,
            'standard_time' => 1,
            'overtime' => null,
            'efficiency' => 100,
            'status' => ServiceOrderProcess::STATUS_COMPLETE,
        ]);

        $service_order->update([
            'status' => ServiceOrder::STATUS_COMPLETE,
            'stage' => ServiceOrderProcess::STAGE_COLLECTED,
            'current_service_order_process_id' => $process->id,
        ]);

        // Redirect back with success message
        return redirect()->back()->with('updated', 'Service order process updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ServiceOrder $service_order
     * @param ServiceOrderProcess $process
     * @return RedirectResponse
     */
    public function destroy(ServiceOrder $service_order, ServiceOrderProcess $process): RedirectResponse
    {
        // Delete the service order
        $process->delete();

        // Redirect back with success message
        return redirect("/service-orders/$service_order->id")->with('deleted', 'Service order process deleted successfully.');
    }
}
