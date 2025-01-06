<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustmentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class StockAdjustmentTypeController extends Controller
{
    public function __construct()
    {
//        $this->authorizeResource(StockAdjustmentType::class, 'type');
    }

    public function index(Request $request)
    {
        $search = $request->search;

        $order = $request->order ?? 'id';
        $by = $request->by ?? 'desc';
        $paginate = $request->paginate ?? 10;

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $stockAdjustmentTypes = StockAdjustmentType::withTrashed()->when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('code', 'LIKE', "%{$search}%");
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];

        $stockAdjustmentTypes->appends($queryString);

        return Inertia::render('Types/Index', [
            'stockAdjustmentTypes' => $stockAdjustmentTypes,
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
        return Inertia::render('Types/Form');
    }

    public function store(Request $request): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'code' => 'required|unique:stock_adjustment_types,code',
            'name' => 'required',
        ]);

        $data = $request->all();
        StockAdjustmentType::create($data);

        return redirect('/stock-adjustment-types')->with('created', 'Stock Adjustment Type created successfully');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param StockAdjustmentType $type
     * @return Response
     */
    public function edit(StockAdjustmentType $type): Response
    {
        return Inertia::render('Types/Form', ['stockAdjustmentType' => $type]);
    }

    public function update(Request $request, StockAdjustmentType $type): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'code' => 'required|unique:stock_adjustment_types,code,' . $type->id,
            'name' => 'required',
        ]);

        $data = $request->all();
        $type->update($data);

        return redirect('/stock-adjustment-types')->with('updated', 'Stock Adjustment Type updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param StockAdjustmentType $type
     * @return RedirectResponse
     */
    public function destroy(StockAdjustmentType $type): RedirectResponse
    {
        $type->delete();

        return back()->with('deleted', 'Stock Adjustment Type inactivated successfully');
    }

    public function restore($type_id) {
        $type = StockAdjustmentType::withTrashed()->find($type_id);
        $type->restore();
        return back()->with('created', 'Stock Adjustment Type activated successfully');
    }

    public function select2Query(Request $request)
    {
        return StockAdjustmentType::when($request->has('search'), function ($query) use ($request) {
            $query->where('name', 'LIKE', "%{$request->search}%")
                ->orWhere('code', 'LIKE', "%{$request->search}%");
        })->limit(10)->get();
    }
}
