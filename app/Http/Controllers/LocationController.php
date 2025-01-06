<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Location::class, 'location');
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

        $locations = Location::with('store')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
                $query->orWhereHas('store', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'LIKE', "%{$search}%");
                });
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

        $locations->appends($queryString);

        return Inertia::render('Location/Index', [
            'locations' => $locations,
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
        $stores = self::toSelect2Data(Store::all()->toArray(), 'id', 'name');
        return Inertia::render('Location/Form', ['stores' => $stores]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:locations',
            'status' => 'required|in:0,1',
            'store_id' => [
                'required',
                Rule::exists('stores', 'id'),
            ],
        ], [
            'name.required' => 'Please enter a name.',
            'name.unique' => 'The name has already been taken. Please choose a different name.',
            'status.required' => 'Please select a valid status (0 or 1).',
            'status.in' => 'Status must be either 0 or 1.',
            'store_id.required' => 'Please select a valid store.',
            'store_id.exists' => 'The selected store does not exist in the database.',
        ]);

        Location::create($request->all());

        return redirect('/locations')->with('created', 'Location created successfully');
    }

    public function edit(Location $location)
    {
        $stores = self::toSelect2Data(Store::all()->toArray(), 'id', 'name');
        return Inertia::render('Location/Form', ['location' => $location, 'stores' => $stores]);
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('locations')->ignore($location->id),
            ],
            'status' => 'required|in:0,1',
        ], [
            'name.required' => 'Please enter a name.',
            'name.unique' => 'The name has already been taken. Please choose a different name.',
            'status.required' => 'Please select a valid status (0 or 1).',
            'status.in' => 'Status must be either 0 or 1.',
        ]);

        $location->update($request->all());

        return redirect('/locations')->with('updated', 'Location updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Location $location
     * @return RedirectResponse
     */
    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();

        return back()->with('deleted', 'Location deleted successfully');
    }

    public function select2Query(Request $request)
    {
        return Location::where('store_id', $request->store_id)->when($request->has('search'), function ($query) use ($request) {
            $query->where('name', 'LIKE', "%{$request->search}%")
                ->orWhere('code', 'LIKE', "%{$request->search}%");
        })->limit(10)->get();
    }
}
