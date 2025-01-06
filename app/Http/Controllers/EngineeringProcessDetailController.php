<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;
use App\Models\Process;
use App\Models\ProcessDetail;
use Illuminate\Support\Facades\Validator;

class EngineeringProcessDetailController extends Controller
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

        $processes = Process::where('type', 'Engineering')->when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('code', 'LIKE', "%{$search}%");
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

        $processes->appends($queryString);

        return Inertia::render('EngineeringProcessDetail/Index', [
            'processes' => $processes,
            'filters' => [],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('EngineeringProcessDetail/Form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'standard_time_hour' => 'required',
            'standard_time_minute' => 'required',
            'manpower' => 'required',
        ]);
        $process = Process::create([
            'name' => $request->process_name,
            'standard_time_hour' => $request->standard_time_hour,
            'standard_time_minute' => $request->standard_time_minute,
            'manpower' => $request->manpower,
            'type' => 'Engineering'
        ]);
        foreach ($request->groups as $group) {
            foreach ($group['items'] as $item) {
                ProcessDetail::insert([
                    'process_id' => $process->id,
                    'group_name' => $group['group_name'],
                    'question' => $item['question_name'],
                    'form_type' => $item['form_type']
                ]);
            }
        }

        return redirect('/engineering-process-detail')->with('created', 'Process created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Vendor $vendor
     * @return Response
     */
    public function edit($process_id): Response
    {
        $process = Process::where('id', $process_id)->with('detail')->first();
        return Inertia::render('EngineeringProcessDetail/Form', [
            'process' => $process,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Vendor $vendor
     * @return Application|Redirector|RedirectResponse
     */
    public function update(Request $request, $process_id): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'standard_time_hour' => 'required',
            'standard_time_minute' => 'required',
            'groups.*.items.*.form_type' => 'required',
        ]);

        $process = Process::findOrFail($process_id);

        $process->update([
            'name' => $request->process_name,
            'standard_time_hour' => $request->standard_time_hour,
            'standard_time_minute' => $request->standard_time_minute,
            'manpower' => $request->manpower
        ]);

        ProcessDetail::where('process_id', $process_id)->delete();

        foreach ($request->groups as $group) {
            foreach ($group['items'] as $item) {
                ProcessDetail::insert([
                    'process_id' => $process->id,
                    'group_name' => $group['group_name'],
                    'question' => $item['question_name'],
                    'form_type' => $item['form_type']
                ]);
            }
        }

        return redirect('/engineering-process-detail')->with('updated', 'Process updated successfully');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param Vendor $vendor
     * @return RedirectResponse
     */
    public function destroy($process_id)
    {
        $process = Process::findOrFail($process_id);
        $process->delete();

        return redirect('/engineering-process-detail')->with('deleted', 'Process deleted successfully');
    }
}
