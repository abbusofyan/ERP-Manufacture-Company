<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\ItemMovement;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class ItemMovementController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Product::class, 'product');
    }

    // public function select2Query(Request $request)
    // {
    //     return Product::with('uom')->when($request->has('search'), function ($query) use ($request) {
    //         $query->where('sku', 'LIKE', "%{$request->search}%")
    //             ->orWhere('name', 'LIKE', "%{$request->search}%");
    //     })->limit(10)->get();
    // }
    //
    // public function select2QueryWithStorage(Request $request)
    // {
    //     return Product::with('uom')->when($request->has('search'), function ($query) use ($request) {
    //         $query->where('sku', 'LIKE', "%{$request->search}%")
    //             ->orWhere('name', 'LIKE', "%{$request->search}%");
    //     })
    //         ->has('storageItems')
    //         ->limit(10)->get();
    // }
    //
    // public function select2QueryByCategory(Request $request)
    // {
    //     return Product::with('uom')->where('category_id', $request->category_id)->when($request->has('search'), function ($query) use ($request) {
    //         $query->where('sku', 'LIKE', "%{$request->search}%")
    //             ->orWhere('name', 'LIKE', "%{$request->search}%");
    //     })->with('prices')->limit(10)->get();
    // }


    public function index(Request $request): Response
    {
        $search = $request->search;

        $store_id = $request->store_id;

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
        $filters['store_id'] = $store_id;

        $typeNames = ItemMovement::TRANSACTION_TYPE_ARRAY;

        $itemMovements = ItemMovement::with('store', 'product.uom')
            ->when($search, function ($query) use ($search, $typeNames) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('sku', 'LIKE', "%{$search}%");
                })->orWhereHas('store', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })->orWhere(function ($subQuery) use ($search, $typeNames) {
                    foreach ($typeNames as $typeId => $typeName) {
                        if (stripos($typeName, $search) !== false) {
                            $subQuery->orWhere('transaction_type', $typeId);
                        }
                    }
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

        $itemMovements->appends($queryString);

        // Load transactions for each ItemMovement
        $itemMovements->getCollection()->transform(function ($item) {
            $item->transaction = $item->transaction(); // Call the transaction() method
            return $item;
        });

        return Inertia::render('ItemMovement/Index', [
            'itemMovements' => $itemMovements,
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
        $categories = self::toSelect2Data(Category::all()->toArray(), 'id', 'prefix', 'name', ' | ');
        $uom = self::toSelect2Data(UnitOfMeasurement::all()->toArray(), 'id', 'code');
        $vendors = self::toSelect2Data(Vendor::all()->toArray(), 'id', 'code', 'name', ' | ');
        return Inertia::render('Product/Form', [
            'categories' => $categories,
            'uom' => $uom,
            'vendors' => $vendors,
            'csrf' => csrf_token(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'required|string|max:255|unique:products,sku,NULL,id,deleted_at,NULL',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'store_id' => 'required',
            'location_id' => 'required',
            'uom_id' => 'required|exists:unit_of_measurements,id',
            'auto_reorder' => 'required|boolean',
            'lead_time' => 'nullable|integer',
            'reorder_level' => 'nullable|integer',
            'remark' => 'nullable|string|max:255',
            'quantity_to_reorder' => 'nullable|integer',
            'file_url' => 'nullable|image|mimes:png,jpg,jpeg',
            // 'prices' => 'required|array|min:1',
            // 'prices.*.vendor_id' => 'required|exists:vendors,id',
            // 'prices.*.price' => 'required|numeric',
            // 'prices.*.lowest_cost' => 'required|numeric',
            // 'prices.*.last_cost' => 'required|numeric',
            // 'prices.*.highest_cost' => 'required|numeric',
            'documents.*' => 'nullable|file|mimes:pdf',
        ], [
            'prices.required' => 'At least one price must be provided.',
            'prices.array' => 'Prices must be an array.',
            'prices.min' => 'At least one price must be provided.',
            'prices.*.vendor_id.required' => 'Vendor ID is required for each price.',
            'prices.*.vendor_id.exists' => 'The selected vendor for a price is invalid.',
            'prices.*.price.required' => 'Price is required for each item.',
            'prices.*.price.numeric' => 'Price must be a number for each item.',
        ]);

        DB::beginTransaction(); // Begin transaction

        try {
            $data = $request->all();

            $data['created_by'] = auth()->user()->id;
            $product = Product::create($data);

            StoreProduct::create([
                'store_id' => $request->store_id,
                'location_id' => $request->location_id,
                'product_id' => $product->id,
            ]);

            /** Update photo **/
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos', []) as $photo) {
                    $name = $photo->getClientOriginalName();
                    $path = $photo->store("product/$product->id", 'public');
                    $is_sales = in_array('MARKETING', array_column(auth()->user()->roles->toArray(), 'name')) ?? false;
                    ProductPhoto::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->user()->id,
                        'path' => $path,
                        'is_sales_photo' => $is_sales
                    ]);
                }
            }

            /** Update documents **/
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents', []) as $document) {
                    $name = $document->getClientOriginalName();
                    $path = $document->store("product/$product->id/documents/", 'public');
                    ProductDocument::create([
                        'name' => $name,
                        'product_id' => $product->id,
                        'file_url' => $path,
                    ]);
                }
            }

            /** Update product price **/
            if ($request->has('prices')) {
                foreach ($request->prices as $price) {
                    if ($price['vendor_id']) {
                        $price['product_id'] = $product->id;
                        $price['price'] = $price['price'];
                        ProductPrice::create($price);
                    }
                }
            }

            DB::commit(); // Commit transaction

            return redirect('/products')->with('created', 'Product created successfully');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction on error

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param Product $product
     * @return Response
     */
    public function edit(Product $product): Response
    {
        $categories = self::toSelect2Data(Category::all()->toArray(), 'id', 'name');
        $uom = self::toSelect2Data(UnitOfMeasurement::all()->toArray(), 'id', 'code');
        $vendors = self::toSelect2Data(Vendor::all()->toArray(), 'id', 'code', 'name', ' | ');
        $prices = $product->prices()->get();
        $documents = $product->documents()->get();

        return Inertia::render('Product/Form', [
            'product' => $product,
            'categories' => $categories,
            'uom' => $uom,
            'vendors' => $vendors,
            'prices' => $prices,
            'documents' => $documents,
            'csrf' => csrf_token(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Product $product
     * @return Application|Redirector|RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'sku' => 'nullable|string|max:255|unique:unit_of_measurements,code,' . $product->id . ',id,deleted_at,NULL',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'uom_id' => 'required|exists:unit_of_measurements,id',
            'auto_reorder' => 'required|boolean',
            'lead_time' => 'nullable|integer',
            'reorder_level' => 'nullable|integer',
            'remark' => 'nullable|string|max:255',
            'quantity_to_reorder' => 'nullable|integer',
            'file_url' => 'nullable|image|mimes:png,jpg,jpeg',
            'prices' => 'required|array|min:1',
            'prices.*.vendor_id' => 'required|exists:vendors,id',
            'prices.*.price' => 'required|numeric',
            'documents.*' => 'nullable|file|mimes:pdf',
        ], [
            'prices.required' => 'At least one price must be provided.',
            'prices.array' => 'Prices must be an array.',
            'prices.min' => 'At least one price must be provided.',
            'prices.*.vendor_id.required' => 'Vendor ID is required for each price.',
            'prices.*.vendor_id.exists' => 'The selected vendor for a price is invalid.',
            'prices.*.price.required' => 'Price is required for each item.',
            'prices.*.price.numeric' => 'Price must be a number for each item.',
        ]);

        $product['updated_by'] = auth()->user()->id;
        $product->update($request->all());

        /** Update product price **/
        if ($request->has('prices')) {
            $product->prices()->forceDelete();
            foreach ($request->prices as $price) {
                $price['product_id'] = $product->id;
                $pp = ProductPrice::create($price);
            }
        }

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos', []) as $photo) {
                $name = $photo->getClientOriginalName();
                $path = $photo->store("product/$product->id", 'public');
                $is_sales = in_array('MARKETING', array_column(auth()->user()->roles->toArray(), 'name')) ?? false;
                ProductPhoto::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->user()->id,
                    'path' => $path,
                    'is_sales_photo' => $is_sales
                ]);
            }
        }

        /** Update documents **/
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents', []) as $document) {
                $name = $document->getClientOriginalName();
                $path = $document->store("product/$product->id/documents/", 'public');
                ProductDocument::create([
                    'name' => $name,
                    'product_id' => $product->id,
                    'file_url' => $path,
                ]);
            }
        }

        /** Delete exist document **/
        if ($request->has('delete_documents')) {
            foreach ($request->delete_documents as $document) {
                $r = ProductDocument::find($document);
                if ($r) $r->delete();
            }
        }

        return redirect('/products')->with('updated', 'Product updated successfully');
    }


    /**
     * Update the specified resource from storage.
     *
     * @param Product $product
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function updateStatus(Product $product): RedirectResponse
    {
        $this->authorize('update-product');
        if (intval($product->status) === 0) {
            $product->update(['status' => 1]);
        } else {
            $product->update(['status' => 0]);
        }

        return redirect('/products')->with('updated', 'Product updated successfully');
    }


    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        $prices = $product->prices()->with('vendor')->get();
        $product->load('prices.vendor', 'category', 'uom', 'storageItems.storage',
            'storageItems.location', 'photos', 'salesPhotos',
            'assembly.materials.product.category',
            'assembly.materials.product.uom',
            'finishGoods.items.product.category',
            'finishGoods.items.product.uom'
        );
        $storeProduct = StoreProduct::with('location', 'store')->where('product_id', $product->id)->get();
        return Inertia::render('Product/Detail', [
            'product' => $product,
            'prices' => $prices,
            'storeProduct' => $storeProduct
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect('/products')->with('deleted', 'Product deleted successfully');
    }


    public function inactive(Request $request): Response
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

        $products = Product::with('category')->with('uom')->where('status', Product::STATUS_INACTIVE)
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $products->appends($queryString);

        return Inertia::render('Product/Index', [
            'products' => $products,
            'filters' => $filters,
        ]);
    }

    public function uploadSalesPhoto(Request $request, Product $product)
    {
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos', []) as $photo) {
                $name = $photo->getClientOriginalName();
                $path = $photo->store("product/$product->id", 'public');
                ProductPhoto::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->user()->id,
                    'path' => $path,
                    'is_sales_photo' => 1
                ]);
            }
        }
        return redirect('/products/' . $product->id)->with('created', 'Photo uploaded successfully');
    }

}
