<?php

namespace App\Http\Controllers;

use App\Models\MilestonesSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MilestonesSettingController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(MilestonesSetting::class, 'milestones_setting');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $order = $request->input('order', 'id');
        $by = $request->input('by', 'desc');
        $paginate = $request->input('paginate', 10);

        $milestones = MilestonesSetting::query()
            ->withTrashed()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('duration', 'LIKE', "%{$search}%");
            })
            ->orderBy($order, $by)
            ->paginate($paginate)
            ->withQueryString();

        $filters = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        return Inertia::render('MilestonesSetting/Index', [
            'milestones' => $milestones,
            'filters' => $filters,
            'csrf' => csrf_token(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('MilestonesSetting/Form', [
            'csrf' => csrf_token(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
        ]);

        MilestonesSetting::create($request->only('name', 'duration'));

        return redirect()->route('milestones-settings.index')->with('success', 'Milestone created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MilestonesSetting $milestonesSetting
     * @return Response
     */
    public function edit(MilestonesSetting $milestonesSetting): Response
    {
        return Inertia::render('MilestonesSetting/Form', [
            'milestone' => $milestonesSetting,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param MilestonesSetting $milestonesSetting
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, MilestonesSetting $milestonesSetting)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
        ]);

        $milestonesSetting->update($request->only('name', 'duration'));

        return redirect()->route('milestones-settings.index')->with('success', 'Milestone updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param MilestonesSetting $milestonesSetting
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(MilestonesSetting $milestonesSetting)
    {
        $milestonesSetting->delete();

        return redirect()->route('milestones-settings.index')->with('success', 'Milestone deleted successfully.');
    }

    /**
     * Restore a soft-deleted milestone.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $milestone = MilestonesSetting::withTrashed()->findOrFail($id);
        $milestone->restore();

        return redirect()->route('milestones-settings.index')->with('success', 'Milestone restored successfully.');
    }
}
