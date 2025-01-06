<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use App\Models\StorageItem;
use App\Models\Location;
use App\Models\Product;
use App\Models\ServiceAppointment;
use App\Models\Store;
use App\Models\StoreProduct;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorageController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Storage::class, 'storage');
    }

    public function select2Query(Request $request)
    {
        return StorageItem::when($request->has('search'), function ($query) use ($request) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    $query->where('sku', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%");
                });
            });
        })
            ->with(['product.category', 'product.uom', 'storage'])
            ->limit(10)
            ->get();
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

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $statuses = Storage::STATUS;

        $storages = Storage::when($search, function ($query) use ($search, $statuses) {
            $query->where('code', 'LIKE', "%{$search}%");
            $query->orWhere(function ($subQuery) use ($search, $statuses) {
                foreach ($statuses as $statusId => $status) {
                    if (stripos($status, $search) !== false) {
                        $subQuery->orWhere('status', $statusId);
                    }
                }
            });
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $storages->appends($queryString);

        return Inertia::render('Storage/Index', [
            'storages' => $storages,
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Storage $storage
     * @return Response
     */
    public function show(Storage $storage): Response
    {
        $storage->load([
            'storageItems.product',
            'storageItems.location.store',
        ]);
        return Inertia::render('Storage/Detail', [
            'storage' => $storage,
            'csrf' => csrf_token(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        /** Get product **/
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

        $show_all = $request->show_all ?? false;

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $products = Product::with('category')->with('uom')
            ->when(!$show_all, function ($query) use ($search) {
                if (empty($search)) {
                    return $query->where('name', 'LIKE', "########################");
                }
                return $query->where('name', 'LIKE', "%{$search}%");
            })
            ->when($show_all, function ($query) use ($search) {
                if (!empty($search)) {
                    return $query->where('name', 'LIKE', "%{$search}%");
                }
                return $query;
            })
            ->when($order && $by, function ($query) use ($order, $by) {
                return $query->orderBy($order, $by);
            })
            ->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
            'show_all' => $show_all,
        ];

        $products->appends($queryString);
        /** End get product **/

        /** Get stores **/
        $locations = Location::with('store')->get()->toArray();
        $location_select = self::toSelect2Data($locations, 'id', 'name');

        return Inertia::render('Storage/Form', [
            'products' => $products,
            'filters' => $filters,
            'locations' => $locations,
            'location_select' => $location_select,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'grn_number' => 'required|string|max:255|unique:storages,grn_number,NULL,id,deleted_at,NULL',
            'selected_product' => 'required|array',
            'selected_product.*.locations' => 'required|array',
            'selected_product.*.locations.*.location_id' => 'required|exists:locations,id',
            'selected_product.*.locations.*.quantity' => 'required|numeric|min:1',
            'selected_product.*.locations.*.cost_price' => 'required|numeric|min:1',
        ], [
            'selected_product.required' => 'At least one price must be provided.',
            'selected_product.array' => 'Prices must be an array.',
            'selected_product.*.locations.required' => 'At least one price must be provided.',
            'selected_product.*.locations.array' => 'Locations must be an array.',
            'selected_product.*.locations.*.location_id.exists' => 'This location has been deleted or does not exist.',
            'selected_product.*.locations.*.quantity.required' => 'Quantity is required.',
            'selected_product.*.locations.*.quantity.numeric' => 'Quantity must be a number.',
            'selected_product.*.locations.*.quantity.min' => 'Quantity must be greater than 0.',
            'selected_product.*.locations.*.cost_price.required' => 'Cost price is required.',
            'selected_product.*.locations.*.cost_price.numeric' => 'Cost price must be a number.',
            'selected_product.*.locations.*.cost_price.min' => 'Cost price must be greater than 0.',
        ]);

        $data = $request->all();
        dd($data);
        $data['product_count'] = count($data['selected_product']);

        $storage = Storage::create($data);

        /** Update items **/
        foreach ($data['selected_product'] as $product) {
            foreach ($product['locations'] as $location) {
                $l = Location::find($location['location_id']);
                if (!$l) continue;

                $existingStorageItem = StorageItem::where([
                    'location_id' => $location['location_id'],
                    'product_id' => $product['product']['id'],
                    'cost_price' => $location['cost_price'],
                ])->first();
                $quantityToUpdate = $existingStorageItem ? intval($existingStorageItem->quantity) + intval($location['quantity']) : $location['quantity'];

                StorageItem::updateOrCreate(
                    [
                        'location_id' => $location['location_id'],
                        'product_id' => $product['product']['id'],
                        'cost_price' => $location['cost_price'],
                    ],
                    [
                        'storage_id' => $storage->id,
                        'store_id' => $l['store_id'],
                        'quantity' => $quantityToUpdate,
                        'cost_price' => $location['cost_price'],
                        'status' => $data['status'],
                    ]
                );
            }
        }

        return redirect('/storages')->with('created', 'Storage created successfully');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Storage $storage
     * @param Request $request
     * @return Response
     */
    public function edit(Storage $storage, Request $request): Response
    {
        /** Get product **/
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

        $show_all = $request->show_all ?? false;

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $products = Product::with('category')->with('uom')
            ->when(!$show_all, function ($query) use ($search) {
                if (empty($search)) {
                    return $query->where('name', 'LIKE', "########################");
                }
                return $query->where('name', 'LIKE', "%{$search}%");
            })
            ->when($show_all, function ($query) use ($search) {
                if (!empty($search)) {
                    return $query->where('name', 'LIKE', "%{$search}%");
                }
                return $query;
            })
            ->when($order && $by, function ($query) use ($order, $by) {
                return $query->orderBy($order, $by);
            })
            ->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
            'show_all' => $show_all,
        ];

        $products->appends($queryString);
        /** End get product **/

        /** Get stores **/
        $locations = Location::with('store')->get()->toArray();
        $location_select = self::toSelect2Data($locations, 'id', 'name');

        $storage = $storage->load([
            'storageItems.product',
            'storageItems.location.store',
        ]);

        return Inertia::render('Storage/Form', [
            'storage' => $storage,
            'products' => $products,
            'filters' => $filters,
            'locations' => $locations,
            'location_select' => $location_select,
        ]);
    }


    public function update(Request $request, Storage $storage)
    {
        $request->validate([
            'grn_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('storages', 'grn_number')->where(function ($query) use ($storage) {
                    return $query->where('id', '!=', $storage->id)->whereNull('deleted_at');
                }),
            ],
            'selected_product' => 'required|array',
            'selected_product.*.locations' => 'required|array',
            'selected_product.*.locations.*.location_id' => 'required|exists:locations,id',
            'selected_product.*.locations.*.quantity' => 'required|numeric|min:1',
            'selected_product.*.locations.*.cost_price' => 'required|numeric|min:1',
        ], [
            'selected_product.required' => 'At least one price must be provided.',
            'selected_product.array' => 'Prices must be an array.',
            'selected_product.*.locations.required' => 'At least one price must be provided.',
            'selected_product.*.locations.array' => 'Locations must be an array.',
            'selected_product.*.locations.*.location_id.exists' => 'This location has been deleted or does not exist.',
            'selected_product.*.locations.*.quantity.required' => 'Quantity is required.',
            'selected_product.*.locations.*.quantity.numeric' => 'Quantity must be a number.',
            'selected_product.*.locations.*.quantity.min' => 'Quantity must be greater than 0.',
            'selected_product.*.locations.*.cost_price.required' => 'Cost price is required.',
            'selected_product.*.locations.*.cost_price.numeric' => 'Cost price must be a number.',
            'selected_product.*.locations.*.cost_price.min' => 'Cost price must be greater than 0.',
        ]);

        $data = $request->all();
        $data['product_count'] = count($data['selected_product']);
        $storage->update($data);

        /** Update items **/
        $storage->storageItems()->forceDelete();
        foreach ($data['selected_product'] as $product) {
            foreach ($product['locations'] as $location) {
                $l = Location::find($location['location_id']);

                StorageItem::create([
                    'storage_id' => $storage->id,
                    'product_id' => $product['product']['id'],
                    'location_id' => $location['location_id'],
                    'store_id' => $l['store_id'],
                    'quantity' => $location['quantity'],
                    'cost_price' => $location['cost_price'],
                    'status' => $data['status'],
                ]);
            }
        }

        return redirect('/storages')->with('updated', 'Storage updated successfully');
    }

    public function assignToBin(Request $request, Storage $storage, StorageItem $storageItem) {
        $request->validate([
            'store_id' => 'required|integer',
            'location_id' => 'required|integer',
            'qty' => ['required', 'integer', function ($attribute, $value, $fail) use ($request) {
                if ($value > $request->input('stock')) {
                    $fail('Assign quantity cannot be greater than actual quantity.');
                }
            }],
            'stock' => 'required|integer',
        ], [
            'store_id.required' => 'Select store',
            'location_id.required' => 'Select location',
            'qty.required' => 'Quantity is required'
        ]);
        DB::beginTransaction();
        try {
            $storageStore = Store::where('name', Store::STORAGE_WAREHOUSE)->first();

            $data = [
                'store_id' => $storageStore->id,
                'location_id' => Location::where('store_id', $storageStore->id)->first()->id,
                'product_id' => $storageItem->product_id,
                // 'vendor_id' => $storageItem->vendor_id
            ];
            $storageStoreProduct = StoreProduct::where($data)->first();

            $storageStoreProduct->update(['stock' => $storageStoreProduct->stock - $request->qty]);

            $data['store_id'] = $request->store_id;
            $data['location_id'] = Location::where('id', $request->location_id)->where('store_id', $request->store_id)->first()->id;

            $storeProduct = StoreProduct::where($data)->first();
            if ($storeProduct) {
                $storeProduct->update(['stock' => $storeProduct->stock + $request->qty]);
            } else {
                $data['stock'] = $request->qty;
                $storeProduct = StoreProduct::create($data);
            }
            $storageItem->update(['quantity' => $storageItem->quantity - $request->qty]);

            DB::commit();
            return redirect()->back()->with('updated', 'Storage assigned to bin successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('deleted', 'Failed to assign storage to bin: ' . $e->getMessage());
        }
    }
}
