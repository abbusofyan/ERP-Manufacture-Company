<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Customer;
use App\Models\ServiceAppointment;
use App\Models\ServiceInvoice;
use App\Models\ServiceMaintain;
use App\Models\ServiceOrderAttachment;
use App\Models\ServiceOrderProcess;
use App\Models\ServiceQuotation;
use App\Models\Vehicle;
use App\Models\ServiceContract;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ServiceScheduleController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param ServiceMaintain $service_schedule
     * @return Response
     */
    public function show(ServiceMaintain $service_schedule): Response
    {
        return Inertia::render('ServiceSchedule/Detail', [
            'schedule' => $service_schedule->load(['vehicle.customer']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ServiceMaintain $service_schedule
     * @return RedirectResponse
     */
    public function update(Request $request, ServiceMaintain $service_schedule)
    {
        // Validation rules with custom messages
        $messages = [
            'date.required' => 'The date field is required.',
            'date.date' => 'The date is not a valid date.',
        ];

        $request->validate([
            'date' => 'required|date',
        ], $messages);

        $data = $request->all();
        $data['date'] = Carbon::parse($data['date']);

        // Update maintain services
        $service_schedule->update($data);

        // Redirect with a success message
        return redirect()->back()->with('updated', 'Service schedule updated successfully.');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // Determine pagination limit
        $prevPaginate = $request->get('prev_paginate', 10);
        $currentPaginate = $request->get('current_paginate', 10);
        $nextPaginate = $request->get('next_paginate', 10);

        // Determine the page number for each pagination
        $prevPage = $request->get('prev_page', 1);
        $currentPage = $request->get('current_page', 1);
        $nextPage = $request->get('next_page', 1);

        // Current month
        $currentMonth = $request->has('current_month') ? Carbon::parse($request->current_month) : Carbon::now();

        // Retrieve contracts for the previous month
        $prevServices = ServiceMaintain::with(['vehicle'])
            ->where('is_active', true)
            ->where('is_draft', false)
            ->whereBetween('date', [
                $currentMonth->copy()->subMonth()->startOfMonth(),
                $currentMonth->copy()->subMonth()->endOfMonth()
            ])
            ->orderBy('date', 'asc')
            ->paginate($prevPaginate, ['*'], 'prev_page', $prevPage)
            ->appends($request->except('prev_page'));

        // Retrieve contracts for the current month
        $currentServices = ServiceMaintain::with(['vehicle'])
            ->where('is_active', true)
            ->where('is_draft', false)
            ->whereBetween('date', [
                $currentMonth->copy()->startOfMonth(),
                $currentMonth->copy()->endOfMonth()
            ])
            ->orderBy('date', 'asc')
            ->paginate($currentPaginate, ['*'], 'current_page', $currentPage)
            ->appends($request->except('current_page'));

        // Retrieve contracts for the next month
        $nextServices = ServiceMaintain::with(['vehicle'])
            ->where('is_active', true)
            ->where('is_draft', false)
            ->whereBetween('date', [
                $currentMonth->copy()->addMonth()->startOfMonth(),
                $currentMonth->copy()->addMonth()->endOfMonth()
            ])
            ->orderBy('date', 'asc')
            ->paginate($nextPaginate, ['*'], 'next_page', $nextPage)
            ->appends($request->except('next_page'));

        // Check if the current month matches the real-time current month
        $now = Carbon::now();

        // Return view with contracts and filters
        return Inertia::render('ServiceSchedule/Index', [
            'prevServices' => $prevServices,
            'currentServices' => $currentServices,
            'nextServices' => $nextServices,
            'monthTexts' => [
                'previous' => [
                    'isCurrentMonth' => $currentMonth->copy()->subMonth()->isSameMonth($now),
                    'value' => $currentMonth->copy()->subMonth()->format('Y-m'),
                    'text' => $currentMonth->copy()->subMonth()->format('F Y'),
                ],
                'current' => [
                    'isCurrentMonth' => $currentMonth->isSameMonth($now),
                    'value' => $currentMonth->format('Y-m'),
                    'text' => $currentMonth->format('F Y'),
                ],
                'next' => [
                    'isCurrentMonth' => $currentMonth->copy()->addMonth()->isSameMonth($now),
                    'value' => $currentMonth->copy()->addMonth()->format('Y-m'),
                    'text' => $currentMonth->copy()->addMonth()->format('F Y'),
                ],
            ],
        ]);
    }
}
