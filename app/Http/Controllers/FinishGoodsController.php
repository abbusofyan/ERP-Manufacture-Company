<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use App\Models\FinishGoods;
use App\Models\FinishGoodsItem;
use App\Models\Product;
use App\Models\UnitOfMeasurement;
use App\Models\Category;
use App\Models\StoreProduct;
use App\Models\Store;
use App\Models\Location;
use App\Models\AssemblyMaterial;
use App\Models\ItemMovement;
use App\Models\FinishGoodsTransaction;
use App\Models\Assembly;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Validator;

class FinishGoodsController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Assembly::class, 'assembly');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
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

        $fgTransaction = FinishGoodsTransaction::with('finishGoods.assembly.product.uom', 'finishGoods.product', 'finishGoods.category')
            ->when($search, function ($query) use ($search) {
                $query->where('code', 'LIKE', "%{$search}%")
                      ->orWhereHas('finishGoods', function ($q) use ($search) {
                          $q->where('code', 'LIKE', "%{$search}%");
                      })
                      ->orWhereHas('finishGoods.assembly.product', function ($q) use ($search) {
                          $q->where('name', 'LIKE', "%{$search}%");
                      })
                      ->orWhereHas('finishGoods.category', function ($q) use ($search) {
                          $q->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('prefix', 'LIKE', "%{$search}%");
                      })
                      ->orWhereHas('finishGoods.assembly.product.uom', function ($q) use ($search) {
                          $q->where('code', 'LIKE', "%{$search}%");
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

        $fgTransaction->appends($queryString);
        return Inertia::render('FinishGoods/Index', [
            'fgTransaction' => $fgTransaction,
            'filters' => [],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
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

        $items = AssemblyMaterial::with('product.uom')->where('assembly_id', $request->assembly_id)->when($search, function ($query) use ($search) {
            $query->whereHas('product', function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('sku', 'LIKE', "%{$search}%");
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

        $items->appends($queryString);
        // $transactionNo = FinishGoodsTransaction::generateTransactionNo();

        $categories = self::toSelect2Data(Category::all()->toArray(), 'id', 'prefix', 'name', ' | ');
        $defaultStore = self::toSelect2Data(Store::where('name', Store::DEFAULT_STORE)->get()->toArray(), 'id', 'name');

        return Inertia::render('FinishGoods/Form', [
            'items' => $items,
            'filters' => [],
            'csrf' => csrf_token(),
            // 'transaction_no' => $transactionNo,
            'categories' => $categories,
            'default_store' => $defaultStore
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): Redirector|RedirectResponse|Application
    {

        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'assembly_id' => 'required',
            'store_id' => 'required',
            'category_id' => 'required',
            'selling_price' => 'required|numeric|min:0.01',
            'convert_qty' => 'required|numeric|min:1',
            // 'items.*.qty_on_stock' => 'required|numeric|min:1',
        ], [
            'assembly_id.required' => 'Select assembly.',
            'category_id.required' => 'Select category.',
            'store_id.required' => 'Select warehouse.',
            'code.required' => 'Finish Group ID is required.',
            'selling_price.required' => 'Selling price is required.',
            'convert_qty.required' => 'Convert Qty is required.',
            'selling_price.min' => 'Selling price is required.',
            // 'items.*.qty_on_stock.min' => 'Insufficient stock.',
        ]);

        // $validator->after(function ($validator) use ($request) {
        //     foreach ($request->input('items', []) as $index => $item) {
        //         if (isset($item['convert_qty'], $item['qty_on_stock']) && $item['convert_qty'] > $item['qty_on_stock']) {
        //             $validator->errors()->add("items.$index.qty_on_stock", 'Insufficient stock.');
        //         }
        //     }
        // });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $data = $request->all();

            $transactionNo = FinishGoodsTransaction::generateTransactionNo();

            $data['created_by'] = auth()->user()->id;
            // $data['convert_no'] = $transactionNo;
            $finishGoods = FinishGoods::where('code', $data['code'])->first();
            if ($finishGoods) {
                $product = Product::where('sku', $finishGoods->code)->first();
                $storeProduct = StoreProduct::where('store_id', $data['store_id'])->where('product_id', $product->id)->first();
                if ($storeProduct) {
                    $storeProduct->update(['stock' => $storeProduct->stock + $data['convert_qty']]);
                } else {
                    StoreProduct::create([
                        'store_id' => $data['store_id'],
                        'location_id' => Location::defaultLocation($data['store_id'])->id,
                        'product_id' => $product->id,
                        'stock' => $data['convert_qty']
                    ]);
                }
                $finishGoods->update(['convert_qty' => $finishGoods->convert_qty + $data['convert_qty']]);
                $product->update(['stock' => $product->stock + $data['convert_qty']]);
            } else {
                $finishGoods = FinishGoods::create($data)->load('assembly.product');

                $product = $finishGoods->assembly->product;
                $product['sku'] = $finishGoods->code;
                $product['stock'] = $finishGoods->convert_qty;
                $product['selling_price'] = $finishGoods->selling_price;
                $inventoryService = new InventoryService;
                $product = $inventoryService->createNewProduct($product->toArray());

                StoreProduct::create([
                    'store_id' => $data['store_id'],
                    'location_id' => Location::defaultLocation($data['store_id'])->id,
                    'product_id' => $product->id,
                    'stock' => $data['convert_qty']
                ]);
            }

            foreach ($request->items as $item) {
                // FinishGoodsItem::create([
                //     'finish_goods_id' => $finishGoods->id,
                //     'product_id' => $item['product_id'],
                //     'qty' => $item['convert_qty']
                // ]);

                ItemMovement::create([
                    'product_id' => $item['product_id'],
                    'store_id' => $data['store_id'],
                    'movement_type' => '-',
                    'transaction_type' => ItemMovement::FINISH_GOODS_TYPE,
                    'transaction_id' => $finishGoods->id,
                    'quantity' => $item['convert_qty'],
                ]);
            }

            FinishGoodsTransaction::create([
                'code' => $transactionNo,
                'finish_goods_id' => $finishGoods->id,
                'convert_qty' => $request->convert_qty,
                'created_by' => auth()->user()->id
            ]);

            DB::commit();

            return redirect('/finish-goods')->with('created', 'Finish Goods created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return redirect('/finish-goods')->with('deleted', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Vendor $vendor
     * @return Response
     */
    public function edit(FinishGoods $finish_good): Response
    {
        $finishGoods = $finish_good;
        $finishGoods->load('items.product.uom');

        $products = Product::with('uom')->get();

        return Inertia::render('FinishGoods/Form', [
            'finishGoods' => $finishGoods,
            'products' => $products
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Vendor $vendor
     * @return Application|Redirector|RedirectResponse
     */
    public function update(Request $request, FinishGoods $finish_good): Redirector|RedirectResponse|Application
    {
        $finishGoods = $finish_good;

        $request->validate([
            'code' => 'required|unique:assemblies,code,' . $finishGoods->id,
            'name' => 'required',
            'category' => 'required',
            'uom' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $finishGoods->update([
                'code' => $request->code,
                'name' => $request->name,
                'category' => $request->category,
                'uom' => $request->uom
            ]);

            FinishGoodsItem::where('finish_goods_id', $finishGoods->id)->delete();

            foreach ($request->materials as $material) {
                FinishGoodsItem::create([
                    'finish_goods_id' => $finishGoods->id,
                    'product_id' => $material['product']['id'],
                    'qty' => $material['qty']
                ]);
            }

            $category = Category::firstOrCreate([
                'name' => $finishGoods->category
            ], [
                'name' => $finishGoods->category,
                'prefix' => 'ASM'
            ]);

            $uom = UnitOfMeasurement::firstOrCreate([
                'code' => $finishGoods->uom
            ], [
                'code' => $finishGoods->uom,
                'description' => 'Assembly Uom'
            ]);

            $product = Product::where('fg_id', $finishGoods->id)->first();
            $product->update([
                'type' => Product::DEFAULT_TYPE,
                'sku' => $finishGoods->code,
                'name' => $finishGoods->name,
                'category_id' => $category->id,
                'uom_id' => $uom->id,
                'auto_reorder' => 0,
                'status' => 1,
                'fg_id' => $finishGoods->id,
                'created_by' => auth()->user()->id
            ]);

            DB::commit();

            return redirect('/finish-goods')->with('updated', 'Finish Goods updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Update failed: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Vendor $vendor
     * @return Response
     */
    public function show(Request $request, FinishGoodsTransaction $finishGoodsTransaction)
    {
        $finishGoodsTransaction->load('createdBy', 'finishGoods.assembly.product', 'finishGoods.assembly.materials.product.uom', 'finishGoods.category', 'finishGoods.items.product.uom');
        return Inertia::render('FinishGoods/Detail', [
            'finishGoodsTransaction' => $finishGoodsTransaction,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Vendor $vendor
     * @return RedirectResponse
     */
    public function destroy(Assembly $assembly)
    {
        $assembly->delete();

        return redirect('/assembly')->with('deleted', 'Assembly deleted successfully');
    }

    public function toggleStatus($fg_id, $status) {
        DB::beginTransaction();

        try {
            $finishGoods = FinishGoods::withoutGlobalScope('active')->findOrFail($fg_id);
            $finishGoods->update([
                'status' => $status,
            ]);

            $product = Product::where('fg_id', $fg_id)->first();
            $product->update(['status' => $status]);

            DB::commit();
            return redirect()->back()->with('updated', 'Finish Goods updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Error updating Finish Goods: ' . $e->getMessage());
        }
    }

    public function select2Query(Request $request)
    {
        return Assembly::with([
                'materials.product.uom',
                'processes'
            ])->when($request->has('search'), function ($query) use ($request) {
            $query->where('code', 'LIKE', "%{$request->search}%")
                ->orWhere('name', 'LIKE', "%{$request->search}%");
        })->limit(10)->get();
    }

    /**
     * Display a listing of the inactive assembly.
     *
     * @return Response
     */
    public function inactive(Request $request)
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

        $assembly = Assembly::withoutGlobalScope('active')->where('status', 'Inactive')->when($search, function ($query) use ($search) {
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

        $assembly->appends($queryString);

        return Inertia::render('Assembly/Inactive', [
            'assemblies' => $assembly,
            'filters' => [],
        ]);
    }

    public function replaceItem() {
        $assemblies = Assembly::with('materials')->get();
        $products = Product::all();

        return Inertia::render('Assembly/ReplaceItem', [
            'assemblies' => $assemblies,
            'products' => $products,
            'csrf' => csrf_token(),
            'filters' => [],
        ]);
    }

    public function getByItem(Request $request)
    {
        $assemblies = Assembly::whereHas('materials', function($query) use($request) {
            $query->where('product_id', $request->product_id);
        })->with('materials', function($query) use($request) {
            $query->where('product_id', $request->product_id);
        })->get();
        return $assemblies;
    }

    public function processReplaceItem(Request $request) {
        $request->validate([
            'existing_item_selected' => 'required',
            'new_item_selected' => 'required',
            'assemblies' => 'required|array',
        ], [
            'existing_item_selected.required' => 'Select existing item.',
            'new_item_selected.required' => 'Select new item.',
            'assemblies.required' => 'Select at least one BOM.',
        ]);
        foreach ($request->assemblies as $assembly) {
            $assemblyMaterial = AssemblyMaterial::where([
                'assembly_id' => $assembly['id'],
                'product_id' => $request->existing_item_selected
            ])->update(['product_id' => $request->new_item_selected]);
        }

        return redirect('/assembly')->with('updated', 'Item replaced successfully');
    }

    public function duplicate(Assembly $assembly) {
        $assembly->load(['materials', 'processes']);
        $code = Assembly::generateCode();
        $newAssembly = Assembly::create([
            'code' => $code,
            'name' => $assembly->name,
            'category' => $assembly->category,
            'uom' => $assembly->uom,
            'status' => 'Active'
        ]);

        foreach ($assembly->processes as $process) {
            AssemblyProcess::create([
                'assembly_id' => $newAssembly->id,
                'process_id' => $process['process_id'],
            ]);
        }

        foreach ($assembly->materials as $material) {
            AssemblyMaterial::create([
                'assembly_id' => $newAssembly->id,
                'product_id' => $material->product_id,
                'qty' => $material->qty
            ]);
        }

        return redirect('/assembly')->with('updated', 'Assembly duplicated successfully');
    }

    public function fetchItemQty(Request $request, Store $store) {
        $data = [];
        foreach ($request->items as $key => $item) {
            $stock = StoreProduct::with('product')->where('product_id', $item['product_id'])->where('store_id', $store->id)->first();
            $item['qty_on_stock'] = $stock ? $stock->stock : 0;
            $item['last_cost'] = $stock ? $stock->product->lastCost() : 0;
            array_push($data, $item);
        }
        return $data;
    }

}
