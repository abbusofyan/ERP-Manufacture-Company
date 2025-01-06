<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use App\Models\StorageItem;
use App\Models\Location;
use App\Models\Product;
use App\Models\Store;
use App\Models\Transfer;
use App\Models\TransferItem;
use App\Models\User;
use App\Models\StoreProduct;
use App\Models\ItemMovement;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Transfer::class, 'transfer');
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

        $statuses = Transfer::STATUS_TEXT_ARRAY;

        $transfer = Transfer::with('originStore', 'destinationStore', 'author')
            ->when($search, function ($query) use ($search, $statuses) {
                $query->where('transfers.code', 'LIKE', "%{$search}%")
                    ->orWhereHas('originStore', function ($originQuery) use ($search) {
                        $originQuery->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('destinationStore', function ($destinationQuery) use ($search) {
                        $destinationQuery->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('author', function ($authorQuery) use ($search) {
                        $authorQuery->where('name', 'LIKE', "%{$search}%");
                    });
                    $query->orWhere(function ($subQuery) use ($search, $statuses) {
                        foreach ($statuses as $statusId => $status) {
                            if (stripos($status, $search) !== false) {
                                $subQuery->orWhere('status', $statusId);
                            }
                        }
                    });
            })
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })
            ->select('transfers.*')
            ->groupBy('transfers.id')
            ->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];

        $transfer->appends($queryString);

        return Inertia::render('Transfer/Index', [
            'transfers' => $transfer,
            'filters' => $filters,
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
        /** Get storage **/
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

        if ($request->has('origin_store_id')) {
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
                ->where('store_id', $request->origin_store_id)
                ->where('stock', '>', 0)
                ->paginate(0);

            $filters['origin_store_id'] = $request->origin_store_id;
            $queryString = [
                'origin_store_id' => $request->origin_store_id,
                'search' => $search,
                'order' => $order,
                'by' => $by,
                'paginate' => 0,
            ];

            $storage_items->appends($queryString);
        }
        /** End get storage **/

        /** Get stores **/
        // $locations = Location::with('store')->get()->toArray();
        $stores = Store::all()->toArray();

        return Inertia::render('Transfer/Form', [
            'storage_items' => $storage_items,
            'filters' => $filters,
            'stores' => $stores,
            'csrf' => csrf_token(),
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'origin_store_id' => 'required|exists:stores,id',
            'destination_store_id' => [
                'required',
                'different:origin_store_id',
                'exists:stores,id',
            ],
            'selected_store_product' => 'required|array|min:1',
            'selected_store_product.*.id' => 'required|exists:store_products,id',
            'selected_store_product.*.quantity_change' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1]; // Extract the index
                    $stock = $request->input("selected_store_product.$index.stock");
                    if ($value > $stock) {
                        $fail("Quantity cannot be greater than the stock.");
                    }
                },
            ],
            // 'selected_store_product.*.quantity_change' => 'required|numeric|min:1',
        ],[
            'destination_store_id.required' => 'Select destination store'
        ]);

        return DB::transaction(function () use ($request) {
            $data = $request->all();

            $data['author'] = Auth::user()->id;
            $data['status'] = Transfer::STATUS_DRAFT;

            $transfer = Transfer::create($data);
            $code = 'IVT' .
                now()->format('Y') .
                now()->format('m') .
                now()->format('d') .
                str_pad($transfer->id, 4, '0', STR_PAD_LEFT);

            $transfer->update(['code' => $code]);

            foreach ($data['selected_store_product'] as $item) {
                $item['transfer_id'] = $transfer->id;
                $item['quantity'] = $item['quantity_change'];
                TransferItem::create($item);
            }

            return redirect('/transfers')->with('created', 'Transfer created successfully');
        });
    }


    /**
     * Display the specified resource.
     *
     * @param Transfer $transfer
     * @return Response
     */
    public function show(Transfer $transfer)
    {
        $transfer->load('author', 'originStore', 'destinationStore');
        $transferItems = TransferItem::where('transfer_id', $transfer->id)->with('product')
            // ->with('storageItem.product', 'storageItem.location.store')
            ->paginate(10);

        $queryString = [
            'search' => '',
            'order' => 'id',
            'by' => 'desc',
            'paginate' => 10
        ];

        $transferItems->appends($queryString);


        return Inertia::render('Transfer/Detail', [
            'transfer' => $transfer,
            'transferItems' => $transferItems,
        ]);
    }

    public function copy(Transfer $transfer)
    {
        $transfer->load(['originStore', 'destinationStore', 'transferItems.storageItem.product']);
        $data = [
            'origin_store_id' => $transfer->origin_store_id,
            'destination_store_id' => $transfer->destination_store_id,
            'selected_storage_items' => $transfer->transferItems->toArray(),
            'remarks' => $transfer->remarks
        ];

        $data['author'] = Auth::user()->id;
        $data['status'] = Transfer::STATUS_DRAFT;
        $newTansfer = Transfer::create($data);

        $code = 'IVT' .
            now()->format('Y') .
            now()->format('m') .
            now()->format('d') .
            str_pad($newTansfer->id, 4, '0', STR_PAD_LEFT);


        $newTansfer->update(['code' => $code]);

        foreach ($data['selected_storage_items'] as $item) {
            $item['transfer_id'] = $newTansfer->id;

            TransferItem::create($item);

            $originStore = StoreProduct::where('store_id', $data['origin_store_id'])->where('product_id', $item['product_id'])->first();
            $originStore->update(['stock' => $originStore->stock - $item['quantity']]);

            $destinationStore = StoreProduct::where('store_id', $data['destination_store_id'])->where('product_id', $item['product_id'])->first();
            if ($destinationStore) {
                $destinationStore->update(['stock' => $destinationStore->stock + $item['quantity']]);
            } else {
                $store = Store::find($data['destination_store_id']);
                $location = Location::where('store_id', $data['destination_store_id'])->where('name', $store->name)->first();
                StoreProduct::create([
                    'store_id' => $store->id,
                    'location_id' => $location->id,
                    'product_id' => $item['product_id'],
                    'stock' => $item['quantity']
                ]);
            }
        }

        return redirect('/transfers')->with('created', 'Transfer copied successfully');
    }

    public function cancel(Request $request, Transfer $transfer) {
        DB::beginTransaction();

        try {
            $transfer->load('transferItems');

            // if ($transfer->status == Transfer::STATUS_DRAFT) {
            //     foreach ($transfer->transferItems as $item) {
            //         $destinationStore = StoreProduct::where('store_id', $transfer->destination_store_id)
            //         ->where('product_id', $item->product_id)
            //         ->first();
            //
            //         $destinationStore->update(['stock' => $destinationStore->stock - $item->quantity]);
            //
            //         $originStore = StoreProduct::where('store_id', $transfer->origin_store_id)
            //         ->where('product_id', $item->product_id)
            //         ->first();
            //
            //         $originStore->update(['stock' => $originStore->stock + $item->quantity]);
            //     }
            // }

            $transfer->update(['status' => Transfer::STATUS_CANCELED]);
            DB::commit();
            return redirect()->back()->with('created', 'Item transfer canceled');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to cancel item transfer');
        }
    }

    public function sendForApproval(Transfer $transfer) {
        $transfer->update(['status' => Transfer::STATUS_CONFIRMED]);
        return redirect()->back()->with('created', 'Transaction has been sent to managr for approval');
    }

    public function approve(Transfer $transfer) {
        DB::beginTransaction();

        try {

            $transfer->load('transferItems');

            foreach ($transfer->transferItems as $item) {
                $originStore = StoreProduct::where('store_id', $transfer->origin_store_id)->where('product_id', $item->product_id)->first();
                $originStore->update(['stock' => $originStore->stock - $item->quantity]);
                $im = ItemMovement::create([
                    'product_id' => $item->product_id,
                    'store_id' => $transfer->origin_store_id,
                    'movement_type' => ItemMovement::MOVEMENT_TYPE_MINUS,
                    'transaction_type' => ItemMovement::INVENTORY_TRANSFER_TYPE,
                    'transaction_id' => $transfer->id,
                    'quantity' => $item->quantity
                ]);

                $destinationStore = StoreProduct::where('store_id', $transfer->destination_store_id)->where('product_id', $item->product_id)->first();
                if ($destinationStore) {
                    $destinationStore->update(['stock' => $destinationStore->stock + $item->quantity]);
                } else {
                    $store = Store::find($transfer->destination_store_id);
                    $location = Location::where('store_id', $transfer->destination_store_id)->where('code', Location::DEFAULT_CODE)->first();
                    StoreProduct::create([
                        'store_id' => $store->id,
                        'location_id' => $location->id,
                        'product_id' => $item->product_id,
                        'stock' => $item->quantity
                    ]);
                }
                ItemMovement::create([
                    'product_id' => $item->product_id,
                    'store_id' => $transfer->destination_store_id,
                    'movement_type' => ItemMovement::MOVEMENT_TYPE_PLUS,
                    'transaction_type' => ItemMovement::INVENTORY_TRANSFER_TYPE,
                    'transaction_id' => $transfer->id,
                    'quantity' => $item->quantity
                ]);
            }

            $transfer->update(['status' => Transfer::STATUS_COMPLETED]);

            DB::commit();
            return redirect()->back()->with('created', 'Item transfer approved');

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return redirect()->back()->with('deleted', 'Failed to approve item transfer');
        }
    }





}
