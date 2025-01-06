<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use App\Models\StoreProduct;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Store::class, 'store');
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

        $stores = Store::when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
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

        $stores->appends($queryString);

        return Inertia::render('Store/Index', [
            'stores' => $stores,
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
        return Inertia::render('Store/Form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:stores',
            'status' => 'required|in:0,1',
        ], [
            'name.required' => 'Please enter a name.',
            'name.unique' => 'The name has already been taken. Please choose a different name.',
            'status.required' => 'Please select a valid status (0 or 1).',
            'status.in' => 'Status must be either 0 or 1.',
        ]);

        Store::create($request->all());

        return redirect('/stores')->with('created', 'Store created successfully');
    }

    public function edit(Store $store)
    {
        return Inertia::render('Store/Form', ['store' => $store]);
    }

    public function update(Request $request, Store $store)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('stores')->ignore($store->id),
            ],
            'status' => 'required|in:0,1',
        ], [
            'name.required' => 'Please enter a name.',
            'name.unique' => 'The name has already been taken. Please choose a different name.',
            'status.required' => 'Please select a valid status (0 or 1).',
            'status.in' => 'Status must be either 0 or 1.',
        ]);

        $store->update($request->all());

        return redirect('/stores')->with('updated', 'Store updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Store $store
     * @return RedirectResponse
     */
    public function destroy(Store $store): RedirectResponse
    {
        $store->delete();

        return back()->with('deleted', 'Store deleted successfully');
    }

    public function select2Query(Request $request)
    {
        return Store::when($request->has('search'), function ($query) use ($request) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        })->limit(10)->get();
    }

    public function select2QueryWithStock(Request $request)
    {
        $productId = $request->product_id;

        return Store::with(['stock' => function ($query) use ($productId) {
            $query->where('product_id', $productId);
        }])->when($request->has('search'), function ($query) use ($request) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        })->limit(10)->get();
    }


    public function select2QueryStoreHasProduct(Request $request)
    {
        return StoreProduct::with('product', 'store')
            ->where('product_id', $request->product_id)
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where(function ($subQuery) use ($request) {
                    $subQuery->where('name', 'LIKE', "%{$request->search}%")
                             ->orWhereHas('store', function ($storeQuery) use ($request) {
                                 $storeQuery->where('name', 'LIKE', "%{$request->search}%");
                             });
                });
            })
            ->limit(10)
            ->get();
    }

    public function storeHasProduct(Product $product) {
        return StoreProduct::where('product_id', $product->id)->get();
    }

    public function getProducts(Request $request) {
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

        $storage_items = null;

        if ($request->has('store_id')) {
            $storage_items = StoreProduct::with('product.photos')
                ->with('store')
                ->when($search, function ($query) use ($search) {
                    if (!empty($search)) {
                        return $query->whereHas('product', function ($productQuery) use ($search) {
                            $productQuery->where('name', 'LIKE', "%{$search}%");
                        });
                    }
                    return $query;
                })
                ->when($order && $by, function ($query) use ($order, $by) {
                    return $query->orderBy($order, $by);
                })
                ->where('store_id', $request->store_id)
                ->where('stock', '>', 0)
                ->paginate(0);

            $filters['store_id'] = $request->store_id;
            $queryString = [
                'store_id' => $request->store_id,
                'search' => $search,
                'order' => $order,
                'by' => $by,
                'paginate' => 0,
            ];

            $storage_items->appends($queryString);
        }
        return $storage_items;
    }


}
