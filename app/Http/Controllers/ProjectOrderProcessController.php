<?php

namespace App\Http\Controllers;

use App\Models\ProjectMaintain;
use App\Models\ProjectOrder;
use App\Models\ProjectOrderProcess;
use App\Models\ProjectOrderProcessTimelog;
use App\Models\ProjectWorkingHour;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class ProjectOrderProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ProjectOrder $project_order
     * @param Request $request
     * @return Response
     */
    public function index(ProjectOrder $project_order, Request $request): Response
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

        // Query to retrieve project orders with related entities
        $processes = ProjectOrderProcess::where('project_order_id', $project_order->id)
            ->with('timelogs')
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

        // Return view with project order requirements and filters
        return Inertia::render('ProjectOrder/Process/View', [
            'csrf' => csrf_token(),
            'stages' => ProjectOrderProcess::STAGES,
            'projectOrder' => $project_order->load(['projectQuotation', 'technician', 'customer', 'vehicles', 'currentProcess']),
            'filters' => $filters,
            'processes' => $processes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param ProjectOrder $project_order
     * @return RedirectResponse
     */
    public function store(Request $request, ProjectOrder $project_order): RedirectResponse
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create a new project order process
        ProjectOrderProcess::create([
            'project_order_id' => $project_order->id,
            'name' => $request->input('name'),
            'manpower' => 1,
            'standard_time' => 1,
            'total_time' => 0,
            'overtime' => null,
            'efficiency' => null,
            'status' => ProjectOrderProcess::STATUS_PENDING,
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Project order process created successfully.');
    }

    public function storeFirst(Request $request, ProjectOrder $project_order): RedirectResponse
    {
        // Validate the request
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
        ]);

        // Create a new project order process
        $process = ProjectOrderProcess::create([
            'stage' => $project_order->stage ?: 1,
            'project_order_id' => $project_order->id,
            'begin_datetime' => Carbon::now(),
            'name' => ProjectOrderProcess::STAGES[$project_order->stage ?: 1]['title'],
            'manpower' => 1,
            'standard_time' => 1,
            'total_time' => 0,
            'overtime' => 0,
            'efficiency' => null,
            'status' => ProjectOrderProcess::STATUS_IN_PROGRESS,
        ]);

        // Create an initial timelog with only start_time
        $process->timelogs()->create([
            'start_time' => now(),
        ]);

        // Update the project order
        $project_order->update([
            'technician_id' => $request->input('user_id'),
            'stage' => $project_order->stage ?: 1,
            'current_project_order_process_id' => $process->id,
        ]);

        // Update the maintain status
        $project_order->maintain()->update([
            'status' => ProjectMaintain::STATUS_IN_SERVICE,
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Project order process created successfully.');
    }

    public function continue(Request $request, ProjectOrder $project_order, ProjectOrderProcess $process): RedirectResponse
    {
        // Update the process status and begin_datetime
        $process->update([
            'begin_datetime' => Carbon::now(),
            'status' => ProjectOrderProcess::STATUS_IN_PROGRESS,
        ]);

        // Create a new timelog with only start_time
        $process->timelogs()->create([
            'start_time' => now(),
        ]);

        if (is_null($project_order->current_project_order_process_id)) {
            $project_order->update([
                'current_project_order_process_id' => $process->id,
            ]);

            $project_order->maintain()->update([
                'status' => ProjectMaintain::STATUS_IN_SERVICE,
            ]);
        }

        // Redirect back with success message
        return redirect()->back()->with('updated', 'Project order process continued successfully.');
    }

    protected function calculateProcessTime(ProjectOrderProcess $process, Carbon $startTime, Carbon $endTime, string $status)
    {
        // Retrieve default working hours, lunch time, and dinner time
        $defaultWorkingHour = ProjectWorkingHour::where('is_default', true)->first();
        $lunchTime = ProjectWorkingHour::where('is_lunch_time', true)->first();
        $dinnerTime = ProjectWorkingHour::where('is_dinner_time', true)->first();

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
            $specificWorkingHour = ProjectWorkingHour::whereDate('specific_date', $date->toDateString())->first();

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
        ProjectOrderProcessTimelog::where('project_order_process_id', $process->id)
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
            ProjectOrderProcessTimelog::create([
                'project_order_process_id' => $process->id,
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

    public function pause(Request $request, ProjectOrder $project_order, ProjectOrderProcess $process): RedirectResponse
    {
        // Retrieve the last timelog with null end_time
        $log = ProjectOrderProcessTimelog::where('project_order_process_id', $process->id)
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
        $this->calculateProcessTime($process, $startTime, $endTime, ProjectOrderProcess::STATUS_PAUSE);

        return redirect()->back()->with('updated', 'Project order process paused successfully.');
    }

    public function completed(Request $request, ProjectOrder $project_order, ProjectOrderProcess $process): RedirectResponse
    {
        // Check if the current process matches
        if ($project_order->currentProcess && $project_order->currentProcess->id === $process->id) {
            $project_order_data = [
                'stage' => intval($project_order->stage) == 4 ? intval($project_order->stage) : (intval($project_order->stage) + 1),
                'current_project_order_process_id' => null,
            ];

            if ($project_order->stage == 1) {
                $request->validate([
                    'delivery_date' => 'required|date',
                ]);

                // Check for first check attachment
                $hasFirstCheckAttachment = $project_order->attachments()
                    ->where('stage', 1)
                    ->exists();

                if (!$hasFirstCheckAttachment) {
                    return redirect()->back()->withErrors(['first_attachment' => 'First check attachment is required.']);
                }

                // Assign delivery_date and status
                $project_order_data['delivery_date'] = Carbon::parse($request->input('delivery_date'));
                $project_order_data['status'] = ProjectOrder::STATUS_IN_PROGRESS;
            } else if ($project_order->stage == 2) {
                $project_order_data['status'] = ProjectOrder::STATUS_LAST_CHECK;
            } else if ($project_order->stage == 3) {
                $project_order_data['status'] = ProjectOrder::STATUS_COLLECTION;
            }

            // Update the project order data
            $project_order->update($project_order_data);
        }

        // Load the current process
        $project_order->load('currentProcess');

        // Retrieve the last timelog with null end_time
        $log = ProjectOrderProcessTimelog::where('project_order_process_id', $process->id)
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
        $this->calculateProcessTime($process, $startTime, $endTime, ProjectOrderProcess::STATUS_COMPLETE);

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
        return redirect()->back()->with('updated', 'Project order process completed successfully.');
    }


    /**
     * Update a resource in storage.
     *
     * @param Request $request
     * @param ProjectOrder $project_order
     * @param ProjectOrderProcess $process
     */
    public function setStandardTime(Request $request, ProjectOrder $project_order, ProjectOrderProcess $process)
    {
        // Validate the request
        $request->validate([
            'standard_time' => 'required|numeric|min:1',
        ]);

        $process->update(['standard_time' => $request->standard_time]);

        // Redirect back with success message
        return redirect()->back()->with('updated', 'Project order process updated successfully.');
    }


    /**
     * Update a resource in storage.
     *
     * @param Request $request
     * @param ProjectOrder $project_order
     * @return RedirectResponse
     */
    public function collected(Request $request, ProjectOrder $project_order): RedirectResponse
    {
        $process = ProjectOrderProcess::create([
            'stage' => ProjectOrderProcess::STAGE_COLLECTED,
            'project_order_id' => $project_order->id,
            'begin_datetime' => Carbon::now(),
            'name' => ProjectOrderProcess::STATUS[ProjectOrderProcess::STAGE_COLLECTED],
            'manpower' => 1,
            'standard_time' => 1,
            'overtime' => null,
            'efficiency' => 100,
            'status' => ProjectOrderProcess::STATUS_COMPLETE,
        ]);

        $project_order->update([
            'status' => ProjectOrder::STATUS_COMPLETE,
            'stage' => ProjectOrderProcess::STAGE_COLLECTED,
            'current_project_order_process_id' => $process->id,
        ]);

        // Redirect back with success message
        return redirect()->back()->with('updated', 'Project order process updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProjectOrder $project_order
     * @param ProjectOrderProcess $process
     * @return RedirectResponse
     */
    public function destroy(ProjectOrder $project_order, ProjectOrderProcess $process): RedirectResponse
    {
        // Delete the project order
        $process->delete();

        // Redirect back with success message
        return redirect("/project-orders/$project_order->id")->with('deleted', 'Project order process deleted successfully.');
    }
}
