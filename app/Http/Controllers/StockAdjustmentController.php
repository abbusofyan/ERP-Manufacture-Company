<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use App\Models\StorageItem;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDocument;
use App\Models\StockAdjustmentItem;
use App\Models\StockAdjustmentType;
use App\Models\Store;
use App\Models\Transfer;
use App\Models\TransferItem;
use App\Models\User;
use App\Models\StoreProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use DB;

class StockAdjustmentController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(StockAdjustment::class, 'stock');
    }

    /**
     * Show the list for resources.
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

        $stockAdjustments = StockAdjustment::with('items', 'type')
            ->when($search, function ($query) use ($search) {
                $query->where('reason', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhereHas('type', function ($typeQuery) use ($search) {
                        $typeQuery->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })
            ->paginate($paginate);


        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];

        return Inertia::render('StockAdjustment/Index', [
            'stockAdjustments' => $stockAdjustments,
            'filters' => $filters,
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('StockAdjustment/Form', [
            'csrf' => csrf_token(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'approved_by' => [
                'required',
                Rule::exists('users', 'id'),
            ],
            'stock_adjustment_type_id' => [
                'required',
                Rule::exists('stock_adjustment_types', 'id'),
            ],
            'items' => 'required|array',
            'items.*.product_id' => 'required',
            'items.*.store_id' => 'required',
            'items.*.price' => 'required|numeric',
            'items.*.adjustment_qty' => [
                'min:1',
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1]; // Extract index from items.*
                    $adjustment = $request->input("items.$index.adjustment");
                    $stock = $request->input("items.$index.stock");

                    if ($adjustment === '-' && $value > $stock) {
                        $fail('The adjustment quantity cannot be greater than the stock when adjustment is "-".');
                    }
                },
            ],
        ],[
            'items.*.store_id.required' => 'Select warehouse.',
            'items.*.adjustment_qty.min' => 'Adjustment qty should be greater than 0.',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::user()->id;

        DB::beginTransaction();

        try {
            $stockAdjustment = StockAdjustment::create($data);

            $code = 'IVT' .
                now()->format('Y') .
                now()->format('m') .
                now()->format('d') .
                str_pad($stockAdjustment->id, 4, '0', STR_PAD_LEFT);
            $stockAdjustment->update(['code' => $code]);

            foreach ($data['items'] as $item) {
                $storeProduct = StoreProduct::where('store_id', $item['store_id'])
                    ->where('product_id', $item['product_id'])
                    ->first();

                $product = Product::find($item['product_id']);
                $beforeQty = $product->quantity_after_committed; // Use `quantity_after_committed`

                $adjustmentQty = $item['adjustment_qty'];
                $afterQty = $item['adjustment'] === '+'
                    ? $beforeQty + $adjustmentQty
                    : $beforeQty - $adjustmentQty;

                $item['before_qty'] = $beforeQty;
                $item['after_qty'] = $afterQty;
                $item['stock_adjustment_id'] = $stockAdjustment->id;

                StockAdjustmentItem::create($item);

                // Update stock in StoreProduct and Product
                if ($storeProduct) {
                    if ($item['adjustment'] === '+') {
                        $storeProduct->update(['stock' => $afterQty]);
                        $product->update(['stock' => $product->stock + $adjustmentQty]);
                    } elseif ($item['adjustment'] === '-') {
                        $storeProduct->update(['stock' => $afterQty]);
                        $product->update(['stock' => $product->stock - $adjustmentQty]);
                    }
                }
            }

            if ($request->hasFile('file_url')) {
                $path = $request->file('file_url')->store("stock-adjustments/$stockAdjustment->id", 'public');
                StockAdjustmentDocument::create([
                    'stock_adjustment_id' => $stockAdjustment->id,
                    'name' => $request->file('file_url')->getClientOriginalName(),
                    'file_url' => $path,
                ]);
            }

            DB::commit();

            return redirect('/stock-adjustments')->with('updated', 'Stock Adjustment created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'Failed to create Stock Adjustment: ' . $e->getMessage()]);
        }
    }

    public static function show(StockAdjustment $stockAdjustment, Request $request)
    {
        $stockAdjustment->load('items.product.uom', 'items.store', 'type', 'approved_by');

        // $search_items = $request->search_items;
        //
        // if ($request->order_items && $request->by_items) {
        //     $order_items = $request->order_items;
        //     $by_items = $request->by_items;
        // } else {
        //     $order_items = 'id';
        //     $by_items = 'desc';
        // }
        //
        // if ($request->paginate_items) {
        //     $paginate_items = $request->paginate_items;
        // } else {
        //     $paginate_items = 10;
        // }
        //
        // $filters_items = $request->only(['search_items']);
        // $filters_items['order_items'] = $order_items;
        // $filters_items['by_items'] = $by_items;
        // $filters_items['paginate_items'] = $paginate_items;
        //
        // $stock_adj_items = StockAdjustmentItem::with('product', 'stockAdjustment.staff', 'type')
        //     ->where('stock_adjustment_id', $stockAdjustment->id)
        //     ->when($search_items, function ($query) use ($search_items) {
        //         $query->where('reason', 'LIKE', "%{$search_items}%")
        //             ->orWhere('code', 'LIKE', "%{$search_items}%");
        //     })
        //     ->when(($order_items && $by_items), function ($query) use ($order_items, $by_items) {
        //         $query->orderBy($order_items, $by_items);
        //     })->paginate($paginate_items);
        //
        // $queryString = [
        //     'search' => $search_items,
        //     'order' => $order_items,
        //     'by' => $by_items,
        //     'paginate' => $paginate_items
        // ];
        //
        // $stock_adj_items->appends($queryString);
        //
        // /** Documents **/
        // $search_documents = $request->search_documents;
        //
        // if ($request->order_documents && $request->by_documents) {
        //     $order_documents = $request->order_documents;
        //     $by_documents = $request->by_documents;
        // } else {
        //     $order_documents = 'id';
        //     $by_documents = 'desc';
        // }
        //
        // if ($request->paginate_documents) {
        //     $paginate_documents = $request->paginate_documents;
        // } else {
        //     $paginate_documents = 10;
        // }
        //
        // $filters_documents = $request->only(['search_documents']);
        // $filters_documents['order_documents'] = $order_documents;
        // $filters_documents['by_documents'] = $by_documents;
        // $filters_documents['paginate_documents'] = $paginate_documents;
        //
        // $stock_adj_documents = StockAdjustmentDocument::where('stock_adjustment_id', $stockAdjustment->id)
        //     ->when($search_documents, function ($query) use ($search_documents) {
        //         $query->where('name', 'LIKE', "%{$search_documents}%");
        //     })
        //     ->when(($order_documents && $by_documents), function ($query) use ($order_documents, $by_documents) {
        //         $query->orderBy($order_documents, $by_documents);
        //     })->paginate($paginate_documents, ['*'], 'page2');
        //
        // $queryString = [
        //     'search' => $search_documents,
        //     'order' => $order_documents,
        //     'by' => $by_documents,
        //     'paginate' => $paginate_documents
        // ];
        //
        // $stock_adj_documents->appends($queryString);

        $stockAdjustmentDocuments = StockAdjustmentDocument::where('stock_adjustment_id', $stockAdjustment->id)->get();

        return Inertia::render('StockAdjustment/Detail', [
            'stockAdjustment' => $stockAdjustment,
            'stockAdjustmentDocuments' => $stockAdjustmentDocuments
            // 'filters_items' => $filters_items,
            // 'stock_adj_items' => $stock_adj_items,
            // 'filters_documents' => $filters_documents,
            // 'stock_adj_documents' => $stock_adj_documents,
        ]);
    }

    public function edit(Request $request, StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load('items.product.prices.vendor', 'items.product.uom', 'type', 'approved_by');
        return Inertia::render('StockAdjustment/Form', [
            'stockAdjustment' => $stockAdjustment,
            'csrf' => csrf_token(),
        ]);
    }

    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        $request->validate([
            'approved_by' => [
                'required',
                Rule::exists('users', 'id'),
            ],
            'stock_adjustment_type_id' => [
                'required',
                Rule::exists('stock_adjustment_types', 'id'),
            ],
            'items' => 'required|array',
            'items.*.product_id' => 'required',
            'items.*.store_id' => 'required',
            'items.*.adjustment_qty' => 'required|numeric',
            'items.*.price' => 'required|numeric',
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::user()->id;

        DB::beginTransaction();

        try {
            // Revert previous stock adjustments to restore original stock
            foreach ($stockAdjustment->items as $existingItem) {
                $storeProduct = StoreProduct::where('store_id', $existingItem->store_id)
                    ->where('product_id', $existingItem->product_id)
                    ->first();

                if ($storeProduct) {
                    if ($existingItem->adjustment === '+') {
                        $storeProduct->update(['stock' => $storeProduct->stock - $existingItem->adjustment_qty]);
                    } elseif ($existingItem->adjustment === '-') {
                        $storeProduct->update(['stock' => $storeProduct->stock + $existingItem->adjustment_qty]);
                    }
                }
            }

            // Delete existing items
            StockAdjustmentItem::where('stock_adjustment_id', $stockAdjustment->id)->delete();

            // Update stock adjustment record
            $stockAdjustment->update($data);

            // Reapply stock adjustments with recalculated quantities
            foreach ($data['items'] as $item) {
                $storeProduct = StoreProduct::where('store_id', $item['store_id'])
                    ->where('product_id', $item['product_id'])
                    ->first();

                $product = Product::find($item['product_id']);

                // Calculate before_qty based on current store stock
                $beforeQty = $storeProduct ? $storeProduct->stock : 0;

                // Calculate after_qty based on adjustment
                $adjustmentQty = $item['adjustment_qty'];
                $afterQty = $item['adjustment'] === '+'
                    ? $beforeQty + $adjustmentQty
                    : $beforeQty - $adjustmentQty;

                $item['before_qty'] = $beforeQty;
                $item['after_qty'] = $afterQty;
                $item['stock_adjustment_id'] = $stockAdjustment->id;

                StockAdjustmentItem::create($item);

                // Update stock in StoreProduct and Product
                if ($storeProduct) {
                    $storeProduct->update(['stock' => $afterQty]);
                }

                if ($product) {
                    $product->update(['stock' => $afterQty + $product->committed_qty]);
                }
            }

            // Handle uploaded documents
            if ($request->hasFile('file_url')) {
                $path = $request->file('file_url')->store("stock-adjustments/$stockAdjustment->id", 'public');
                StockAdjustmentDocument::create([
                    'stock_adjustment_id' => $stockAdjustment->id,
                    'name' => $request->file('file_url')->getClientOriginalName(),
                    'file_url' => $path,
                ]);
            }

            DB::commit();

            return redirect('/stock-adjustments')->with('updated', 'Stock Adjustment updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'Failed to update Stock Adjustment: ' . $e->getMessage()]);
        }
    }
}
