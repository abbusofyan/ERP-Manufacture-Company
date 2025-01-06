<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\VendorAddress;
use App\Models\VendorPoc;
use App\Models\Category;
use App\Models\UnitOfMeasurement;
use App\Models\StoreProduct;
use App\Models\Store;
use App\Models\Location;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;
use App\Models\Assembly;
use App\Models\AssemblyProcess;
use App\Models\AssemblyMaterial;
use App\Models\Process;
use App\Models\ProductPhoto;
use App\Models\ProductDocument;
use Illuminate\Support\Facades\DB;
use App\Services\InventoryService;

class AssemblyController extends Controller
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

        $assembly = Assembly::with('product.category', 'product.uom')
            ->whereHas('product', function ($query) use ($search) {
                $query->where('status', Product::STATUS_ACTIVE)
                      ->where(function ($query) use ($search) {
                          $query->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('sku', 'LIKE', "%{$search}%");
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

        $assembly->appends($queryString);

        return Inertia::render('Assembly/Index', [
            'assemblies' => $assembly,
            'filters' => [],
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
        $products = Product::limit(100)->with('uom')->get();
        return Inertia::render('Assembly/Form', [
            'products' => $products,
            'categories' => $categories,
            'uom' => $uom,
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
    public function store(Request $request): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'type' => 'required',
            'code' => 'required|unique:assemblies,code',
            'name' => 'required',
            'category_id' => 'required',
            'uom_id' => 'required',
        ],[
            'category_id.required' => 'Select a category',
            'uom_id.required' => 'Select UOM',
            'code.required' => 'Item ID is required',
        ]);

        DB::beginTransaction();
        try {
            $assembly = Assembly::create([
                'code' => $request->code,
            ]);
            if (isset($request->processes)) {
                foreach ($request->processes as $process) {
                    AssemblyProcess::create([
                        'assembly_id' => $assembly->id,
                        'process_id' => $process['id'],
                    ]);
                }
            }

            if (isset($request->materials)) {
                foreach ($request->materials as $material) {
                    AssemblyMaterial::create([
                        'assembly_id' => $assembly->id,
                        'product_id' => $material['product']['id'],
                        'qty' => $material['qty']
                    ]);
                }
            }


            $data = $request->all();

            $data['sku'] = $assembly->code;
            $data['auto_reorder'] = 0;
            $data['created_by'] = auth()->user()->id;
            $data['assembly_id'] = $assembly->id;

            $inventoryService = new InventoryService;

            $product = $inventoryService->createNewProduct($data);

            if ($request->hasFile('photos')) {
                $inventoryService->uploadProductPhoto($request->file('photos'), $product);
            }

            if ($request->hasFile('documents')) {
                $inventoryService->uploadDocument($request->file('documents'), $product);
            }

            DB::commit();

            return redirect('/assembly')->with('created', 'Assembly created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/assembly')->with('deleted', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Vendor $vendor
     * @return Response
     */
    public function edit(Assembly $assembly): Response
    {
        $assembly->load('product', 'processes.process.detail', 'materials.product.uom');
        $categories = self::toSelect2Data(Category::all()->toArray(), 'id', 'name');
        $uom = self::toSelect2Data(UnitOfMeasurement::all()->toArray(), 'id', 'code');
        $products = Product::limit(10)->with('uom')->get();
        return Inertia::render('Assembly/Form', [
            'assembly' => $assembly,
            'categories' => $categories,
            'uom' => $uom,
            'products' => $products,
            'csrf' => csrf_token()
        ]);
    }


    public function update(Request $request, Assembly $assembly): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'type' => 'required',
            'code' => 'required|unique:assemblies,code,' . $assembly->id,
            'name' => 'required',
            'category_id' => 'required',
            'uom_id' => 'required',
        ],[
            'category_id.required' => 'Select a category',
            'uom_id.required' => 'Select UOM',
            'code.required' => 'Item ID is required',
        ]);
        DB::beginTransaction();

        try {

            $assembly->load('product');

            $assembly->update([
                'code' => $request->code,
            ]);

            AssemblyProcess::where('assembly_id', $assembly->id)->delete();

            foreach ($request->processes as $process) {
                AssemblyProcess::create([
                    'assembly_id' => $assembly->id,
                    'process_id' => $process['id'],
                ]);
            }

            AssemblyMaterial::where('assembly_id', $assembly->id)->delete();

            foreach ($request->materials as $material) {
                AssemblyMaterial::create([
                    'assembly_id' => $assembly->id,
                    'product_id' => $material['product']['id'],
                    'qty' => $material['qty']
                ]);
            }

            $product = $assembly->product;
            $product['sku'] = $request->code;
            $product['updated_by'] = auth()->user()->id;
            $product->update($request->all());

            $inventoryService = new InventoryService;

            if ($request->hasFile('photos')) {
                $inventoryService->uploadProductPhoto($request->file('photos'), $product);
            }

            if ($request->hasFile('documents')) {
                $inventoryService->uploadDocument($request->file('documents'), $product);
            }

            DB::commit();
            return redirect('/assembly')->with('updated', 'Assembly updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            dd('Error : ', $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to update assembly: ' . $e->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Vendor $vendor
     * @return Response
     */
    public function show(Request $request, Assembly $assembly)
    {
        // $assembly = Assembly::withoutGlobalScope('active')->find($assembly_id);

        $assembly->load('product.category', 'product.salesPhotos', 'product.photos', 'product.uom', 'processes.process.detail', 'materials.product.uom');
        $products = [];

        return Inertia::render('Assembly/Detail', [
            'assembly' => $assembly,
            'products' => $products
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

    public function toggleStatus($assembly_id, $status) {
        $assembly = Assembly::withoutGlobalScope('active')->findOrFail($assembly_id);
        $assembly->update([
            'status' => $status,
        ]);
        return redirect()->back()->with('updated', 'Assembly updated successfully');
    }

    public function select2Query(Request $request)
    {
        return Assembly::with([
                // 'materials.product.uom',
                // 'product.stores',
                'product.uom'
        ])->when($request->has('search'), function ($query) use ($request) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('sku', 'LIKE', "%{$request->search}%");
            });
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

        $assembly = Assembly::with('product')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%");
            })
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })
            ->whereHas('product', function ($query) {
                $query->where('status', 'Active');
            })
            ->paginate($paginate);


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

    public function duplicate(Assembly $assembly)
    {
        $assembly->load(['materials', 'processes', 'product']);
        $newAssembly = Assembly::create([
            'code' => $assembly->code . ' (Copy)',
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

        if ($assembly->product) {
            $newProduct = $assembly->product->replicate();
            $newProduct->sku = $newAssembly->code;
            $newProduct->save();

            $newAssembly->update(['product_id' => $newProduct->id]);
        }

        return redirect('/assembly')->with('updated', 'Assembly duplicated successfully');
    }

    public function getItems(Assembly $assembly) {
        // $assemblyMaterials = AssemblyMaterial::with('product.uom')
        //     ->where('assembly_id', $assembly->id)
        //     ->get();
        //
        // $pastCosts = $assemblyMaterials->map(function ($material) {
        //     return $material->product->pastCost() ?? null;
        // });
        //
        // $pastCostsArray = $pastCosts->toArray();
        return AssemblyMaterial::with('product.uom')->where('assembly_id', $assembly->id)->get();
    }

    public function updateItem() {
        return Inertia::render('Assembly/UpdateItem', [
            'csrf' => csrf_token(),
        ]);
    }

}
