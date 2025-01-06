<?php

namespace App\Http\Controllers;

use App\Models\ProjectWorkingHour;
use Carbon\Carbon;
use Dompdf\Image\Cache;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class ProjectWorkingHourController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve month and year from the request, defaulting to current month and year
        $month = $request->input('month', date('n')); // 'n' returns month without leading zeros
        $year = $request->input('year', date('Y'));

        $filters = compact('month', 'year');

        // Generate the start and end dates for the selected month
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Generate an array of all dates in the selected month
        $dates = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dates[] = $date->copy();
        }

        // Fetch default working hours, lunch times, and dinner times
        $defaultWorkingHours = ProjectWorkingHour::where('is_default', true)->first();
        $defaultLunchTime = ProjectWorkingHour::where('is_lunch_time', true)->first();
        $defaultDinnerTime = ProjectWorkingHour::where('is_dinner_time', true)->first();

        $workingHours = [];

        foreach ($dates as $date) {
            $dateStr = $date->format('Y-m-d');

            // Fetch specific working hours for the date, if any
            $specificWH = ProjectWorkingHour::where('specific_date', $dateStr)->first();

            // Use specific working hours or default
            $workingHoursForDate = $specificWH ?? $defaultWorkingHours;

            // Use specific break times or default
            $lunchTime = $defaultLunchTime;
            $dinnerTime = $defaultDinnerTime;

            // Calculate total working minutes
            $totalWorkingMinutes = 0;
            if ($workingHoursForDate) {
                $startTime = Carbon::parse($dateStr . ' ' . $workingHoursForDate->start_time);
                $endTime = Carbon::parse($dateStr . ' ' . $workingHoursForDate->end_time);
                $totalWorkingMinutes += $endTime->diffInMinutes($startTime);
            }

            // Calculate total break minutes (lunch and dinner)
            $totalBreakMinutes = 0;
            if ($lunchTime) {
                $lunchStart = Carbon::parse($dateStr . ' ' . $lunchTime->start_time);
                $lunchEnd = Carbon::parse($dateStr . ' ' . $lunchTime->end_time);
                $totalBreakMinutes += $lunchEnd->diffInMinutes($lunchStart);
            }

            if ($dinnerTime) {
                $dinnerStart = Carbon::parse($dateStr . ' ' . $dinnerTime->start_time);
                $dinnerEnd = Carbon::parse($dateStr . ' ' . $dinnerTime->end_time);
                $totalBreakMinutes += $dinnerEnd->diffInMinutes($dinnerStart);
            }

            // Calculate net working minutes
            $netWorkingMinutes = $totalWorkingMinutes - $totalBreakMinutes;
            $dayHours = $netWorkingMinutes > 0 ? round($netWorkingMinutes / 60, 2) : 0;

            // Determine the earliest start time and latest end time for display
            $startTimeDisplay = $workingHoursForDate ? $workingHoursForDate->start_time : null;
            $endTimeDisplay = $workingHoursForDate ? $workingHoursForDate->end_time : null;

            $workingHours[] = [
                'specificWH' => $specificWH,
                'date' => $dateStr,
                'start_time' => $startTimeDisplay,
                'end_time' => $endTimeDisplay,
                'day_hours' => $dayHours,
            ];
        }

        // Fetch all default times for editing purposes
        $defaultTimes = ProjectWorkingHour::where('is_default', true)
            ->orWhere('is_lunch_time', true)
            ->orWhere('is_dinner_time', true)
            ->get();

        return Inertia::render('ProjectWorkingHour/Index', [
            'workingHours' => $workingHours,
            'filters' => $filters,
            'defaultTimes' => $defaultTimes,
        ]);
    }


    public function create(Request $request): Response
    {
        $date = $request->input('date', date('Y-m-d'));
        return Inertia::render('ProjectWorkingHour/Form', [
            'date' => $date,
            'default' => ProjectWorkingHour::where('is_default', true)->first(),
            'hasDefault' => ProjectWorkingHour::where('is_default', true)->exists(),
            'hasLunchTime' => ProjectWorkingHour::where('is_lunch_time', true)->exists(),
            'hasDinnerTime' => ProjectWorkingHour::where('is_dinner_time', true)->exists(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_time' => 'required|array',
            'end_time' => 'required|array',
            'specific_date' => 'nullable|date',
            'is_default' => 'sometimes|boolean',
            'is_lunch_time' => 'sometimes|boolean',
            'is_dinner_time' => 'sometimes|boolean',
        ]);

        $data = $request->all();

        // Define time flags
        $flags = ['is_default', 'is_lunch_time', 'is_dinner_time'];

        foreach ($flags as $flag) {
            if ($request->has($flag) && $request->$flag) {
                $defaultExists = ProjectWorkingHour::where($flag, true)->exists();
                if ($defaultExists) {
                    return redirect()->back()->withErrors([$flag => "A default {$flag} already exists."]);
                }
            }
        }

        // Convert start_time and end_time arrays to time strings
        $data['start_time'] = $this->convertToTime($request->start_time);
        $data['end_time'] = $this->convertToTime($request->end_time);
        $data['specific_date'] = $data['specific_date'] ? Carbon::parse($data['specific_date']) : null;

        ProjectWorkingHour::create($data);

        return redirect()->route('project-working-hours.index')->with('created', 'Working hours created successfully');
    }

    public function edit(ProjectWorkingHour $project_working_hour): Response
    {
        return Inertia::render('ProjectWorkingHour/Form', [
            'hasDefault' => ProjectWorkingHour::where('is_default', true)->exists(),
            'hasLunchTime' => ProjectWorkingHour::where('is_lunch_time', true)->exists(),
            'hasDinnerTime' => ProjectWorkingHour::where('is_dinner_time', true)->exists(),
            'workingHour' => $project_working_hour
        ]);
    }

    public function update(Request $request, ProjectWorkingHour $project_working_hour): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'start_time' => 'required|array',
            'end_time' => 'required|array',
            'specific_date' => 'nullable|date',
            'is_default' => 'sometimes|boolean',
        ]);

        $data = $request->all();

        // Define time flags
        $flags = ['is_default', 'is_lunch_time', 'is_dinner_time'];

        foreach ($flags as $flag) {
            if ($request->has($flag) && $request->$flag) {
                $defaultExists = ProjectWorkingHour::whereNull('specific_date')
                    ->where($flag, true)
                    ->where('id', '!=', $project_working_hour->id)
                    ->exists();
                if ($defaultExists) {
                    return redirect()->back()->withErrors(['start_time' => "A default {$flag} already exists."]);
                }
                $data['specific_date'] = null;
                ProjectWorkingHour::whereNull('specific_date')
                    ->where($flag, true)
                    ->where('id', '!=', $project_working_hour->id)
                    ->delete();
            }
        }

        // Convert start_time and end_time arrays to time strings
        $data['start_time'] = $this->convertToTime($request->start_time);
        $data['end_time'] = $this->convertToTime($request->end_time);
        $data['specific_date'] = $data['specific_date'] ? Carbon::parse($data['specific_date']) : null;

        $project_working_hour->update($data);

        return redirect()->route('project-working-hours.index')->with('updated', 'Working hours updated successfully');
    }

    public function destroy(ProjectWorkingHour $project_working_hour): RedirectResponse
    {
        $project_working_hour->delete();

        return back()->with('deleted', 'Working hours deleted successfully');
    }

    /**
     * Convert time array (with hours, minutes, seconds) to time string (HH:MM:SS)
     *
     * @param array $time
     * @return string
     */
    protected function convertToTime(array $time): string
    {
        $hours = str_pad($time['hours'], 2, '0', STR_PAD_LEFT);
        $minutes = str_pad($time['minutes'], 2, '0', STR_PAD_LEFT);
        $seconds = str_pad($time['seconds'], 2, '0', STR_PAD_LEFT);

        return "{$hours}:{$minutes}:{$seconds}";
    }
}
