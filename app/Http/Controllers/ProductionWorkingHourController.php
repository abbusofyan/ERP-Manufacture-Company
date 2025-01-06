<?php

namespace App\Http\Controllers;

use App\Models\ProductionWorkingHour;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class ProductionWorkingHourController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));

        $filters = compact('month', 'year');

        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $dates = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dates[] = $date->copy();
        }

        $defaultWorkingHours = ProductionWorkingHour::where('is_default', true)->first();
        $defaultLunchTime = ProductionWorkingHour::where('is_lunch_time', true)->first();
        $defaultDinnerTime = ProductionWorkingHour::where('is_dinner_time', true)->first();

        $workingHours = [];

        foreach ($dates as $date) {
            $dateStr = $date->format('Y-m-d');

            $specificWH = ProductionWorkingHour::where('specific_date', $dateStr)->first();
            $workingHoursForDate = $specificWH ?? $defaultWorkingHours;

            $lunchTime = $defaultLunchTime;
            $dinnerTime = $defaultDinnerTime;

            $totalWorkingMinutes = 0;
            if ($workingHoursForDate) {
                $startTime = Carbon::parse($dateStr . ' ' . $workingHoursForDate->start_time);
                $endTime = Carbon::parse($dateStr . ' ' . $workingHoursForDate->end_time);
                $totalWorkingMinutes += $endTime->diffInMinutes($startTime);
            }

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

            $netWorkingMinutes = $totalWorkingMinutes - $totalBreakMinutes;
            $dayHours = $netWorkingMinutes > 0 ? round($netWorkingMinutes / 60, 2) : 0;

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

        $defaultTimes = ProductionWorkingHour::where('is_default', true)
            ->orWhere('is_lunch_time', true)
            ->orWhere('is_dinner_time', true)
            ->get();

        return Inertia::render('ProductionWorkingHour/Index', [
            'workingHours' => $workingHours,
            'filters' => $filters,
            'defaultTimes' => $defaultTimes,
        ]);
    }


    public function create(Request $request): Response
    {
        $date = $request->input('date', date('Y-m-d'));
        return Inertia::render('ProductionWorkingHour/Form', [
            'date' => $date,
            'default' => ProductionWorkingHour::where('is_default', true)->first(),
            'hasDefault' => ProductionWorkingHour::where('is_default', true)->exists(),
            'hasLunchTime' => ProductionWorkingHour::where('is_lunch_time', true)->exists(),
            'hasDinnerTime' => ProductionWorkingHour::where('is_dinner_time', true)->exists(),
        ]);
    }

    public function store(Request $request)
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
                $defaultExists = ProductionWorkingHour::where($flag, true)->exists();
                if ($defaultExists) {
                    return redirect()->back()->withErrors([$flag => "A default {$flag} already exists."]);
                }
            }
        }

        $data['start_time'] = $this->convertToTime($request->start_time);
        $data['end_time'] = $this->convertToTime($request->end_time);
        $data['specific_date'] = $data['specific_date'] ? Carbon::parse($data['specific_date']) : null;

        ProductionWorkingHour::create($data);

        return redirect()->route('production-working-hours.index')->with('created', 'Working hours created successfully');
    }

    public function edit(ProductionWorkingHour $production_working_hour): Response
    {
        return Inertia::render('ProductionWorkingHour/Form', [
            'hasDefault' => ProductionWorkingHour::where('is_default', true)->exists(),
            'hasLunchTime' => ProductionWorkingHour::where('is_lunch_time', true)->exists(),
            'hasDinnerTime' => ProductionWorkingHour::where('is_dinner_time', true)->exists(),
            'workingHour' => $production_working_hour
        ]);
    }

    public function update(Request $request, ProductionWorkingHour $production_working_hour): Redirector|RedirectResponse|Application
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
                $defaultExists = ProductionWorkingHour::whereNull('specific_date')
                    ->where($flag, true)
                    ->where('id', '!=', $production_working_hour->id)
                    ->exists();
                if ($defaultExists) {
                    return redirect()->back()->withErrors(['start_time' => "A default {$flag} already exists."]);
                }
                $data['specific_date'] = null;
                ProductionWorkingHour::whereNull('specific_date')
                    ->where($flag, true)
                    ->where('id', '!=', $production_working_hour->id)
                    ->delete();
            }
        }

        $data['start_time'] = $this->convertToTime($request->start_time);
        $data['end_time'] = $this->convertToTime($request->end_time);
        $data['specific_date'] = $data['specific_date'] ? Carbon::parse($data['specific_date']) : null;

        $production_working_hour->update($data);

        return redirect()->route('production-working-hours.index')->with('updated', 'Working hours updated successfully');
    }

    public function destroy(ProductionWorkingHour $production_working_hour): RedirectResponse
    {
        $production_working_hour->delete();

        return back()->with('deleted', 'Working hours deleted successfully');
    }

    protected function convertToTime(array $time): string
    {
        $hours = str_pad($time['hours'], 2, '0', STR_PAD_LEFT);
        $minutes = str_pad($time['minutes'], 2, '0', STR_PAD_LEFT);
        $seconds = str_pad($time['seconds'], 2, '0', STR_PAD_LEFT);

        return "{$hours}:{$minutes}:{$seconds}";
    }
}
