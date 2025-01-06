<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\UnitOfMeasurement;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class UnitOfMeasurementController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(UnitOfMeasurement::class, 'uom');
    }

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

        $uom = UnitOfMeasurement::withTrashed()->when($search, function ($query) use ($search) {
            $query->where('code', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];

        $uom->appends($queryString);

        return Inertia::render('UOM/Index', [
            'uom' => $uom,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('UOM/Form');
    }

    public function store(Request $request): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'code' => 'required|unique:unit_of_measurements,code,NULL,id,deleted_at,NULL',
            'description' => 'required',
        ]);

        $data = $request->all();

        UnitOfMeasurement::create($data);

        return redirect('/uom')->with('created', 'UOM created successfully');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param UnitOfMeasurement $uom
     * @return Response
     */
    public function edit(UnitOfMeasurement $uom): Response
    {
        return Inertia::render('UOM/Form', ['uom' => $uom]);
    }

    public function update(Request $request, UnitOfMeasurement $uom): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'code' => 'required|unique:unit_of_measurements,code,' . $uom->id . ',id,deleted_at,NULL',
            'description' => 'required',
        ]);

        $data = $request->all();

        $uom->update($data);

        return redirect('/uom')->with('created', 'UOM created successfully');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param UnitOfMeasurement $uom
     * @return RedirectResponse
     */
    public function destroy(UnitOfMeasurement $uom): RedirectResponse
    {
        $uom->delete();

        return back()->with('deleted', 'UOM inactivated successfully');
    }

    public function restore($uom_id) {
        $uom = UnitOfMeasurement::withTrashed()->find($uom_id);
        $uom->restore();
        return back()->with('created', 'UOM activated successfully');
    }

    public function select2Query(Request $request)
    {
        return UnitOfMeasurement::when($request->has('search'), function ($query) use ($request) {
            $query->where('description', 'LIKE', "%{$request->search}%")
                ->orWhere('code', 'LIKE', "%{$request->search}%");
        })->limit(10)->get();
    }
}
