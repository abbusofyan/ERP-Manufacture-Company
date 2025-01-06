<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProjectMaintain;
use App\Models\ProjectOrderProcess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectTechnicianController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param ProjectOrderProcess $project_technician
     * @return Response
     */
    public function show(ProjectOrderProcess $project_technician): Response
    {
        return Inertia::render('ProjectTechnician/Detail', [
            'process' => $project_technician->load(['projectOrder.vehicle']),
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
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

        $stage = $request->get('stage', ProjectOrderProcess::STAGE_FIRST_CHECK);

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $processes = ProjectOrderProcess::with(['projectOrder.vehicle', 'projectOrder.currentProcess', 'projectOrder.technician', 'projectOrder.customer'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->when($stage, function ($query) use ($stage) {
                $query->where('stage', $stage);
            })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $processes->appends($queryString);

        $stages = ProjectOrderProcess::STAGES;
        unset($stages[ProjectOrderProcess::STAGE_PENDING_COLLECTION]);

        return Inertia::render('ProjectTechnician/Index', [
            'processes' => $processes,
            'filters' => $filters,
            'stage' => $stage,
            'stages' => $stages,
        ]);
    }
}
