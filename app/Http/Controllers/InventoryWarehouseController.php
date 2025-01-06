<?php

namespace App\Http\Controllers;

use App\Models\StoreProduct;
use App\Models\Product;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class InventoryWarehouseController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->search;

        $store_id = $request->store_id;

        if ($request->order && $request->by) {
            $order = $request->order;
            $by = $request->by;
        } else {
            $order = 'product_id';
            $by = 'asc';
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
        $filters['store_id'] = $store_id;

        $products = StoreProduct::with('product.uom', 'store', 'location')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('store', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('location', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('product', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('sku', 'LIKE', "%{$search}%");
                });
            })
            ->when($store_id, function ($query) use ($store_id) {
                $query->where('store_id', $store_id);
            })
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })
            ->paginate($paginate);


        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $products->appends($queryString);

        return Inertia::render('InventoryWarehouse/Index', [
            'products' => $products,
            'filters' => $filters,
            'csrf' => csrf_token(),
        ]);
    }
}
