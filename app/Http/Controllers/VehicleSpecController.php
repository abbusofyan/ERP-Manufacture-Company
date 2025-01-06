<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;
use App\Models\Product;
use App\Models\Assembly;
use App\Models\AssemblyProcess;
use App\Models\AssemblyMaterial;
use App\Models\Process;
use App\Models\VehicleSpec;
use App\Models\VehicleSpecItem;
use App\Models\VehicleSpecHeader;

class VehicleSpecController extends Controller
{
    public function __construct()
    {
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

        $specs = VehicleSpec::when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('code', 'LIKE', "%{$search}%");
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

        $specs->appends($queryString);

        return Inertia::render('VehicleSpec/Index', [
            'specs' => $specs,
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        $headers = VehicleSpecHeader::all();
        return Inertia::render('VehicleSpec/Form', [
            'csrf' => csrf_token(),
            'headers' => $headers
        ]);
    }

    public function store(Request $request): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'name' => 'required',
            'groups.*.items.*.description' => 'nullable|string',
        ], [
            'required' => 'The :attribute field is required.',
            'integer' => 'The :attribute field must be an integer.',
            'string' => 'The :attribute field must be a string.',
            'numeric' => 'The :attribute field must be a number.',
            'array' => 'The :attribute field must be an array.',
            'nullable' => 'The :attribute field can be empty.',
            'boolean' => 'The :attribute field must be true or false.',
        ]);

        $code = VehicleSpec::generateCode();
        $spec = VehicleSpec::create([
            'code' => $code,
            'name' => $request->name,
            'status' => 'Active'
        ]);

        foreach ($request->groups as $header) {
            foreach ($header['items'] as $item) {
                $data = [
                    'vehicle_spec_id' => $spec->id,
                    'header_id' => $header['header_id'],
                    'qty' => $item['qty'],
                    'remark' => $item['remark'],
                    'product_name' => $item['product_name'],
                    'description' => $item['description'],
                ];

                if ($item['item_id']) {
                    if ($item['type'] == 'Assembly Item') {
                        $data['assembly_id'] = $item['item_id'];
                    }
                    if ($item['type'] == 'Single Item') {
                        $data['product_id'] = $item['item_id'];
                    }
                }

                VehicleSpecItem::create($data);
            }
        }

        return redirect('/vehicle-spec')->with('created', 'Vehicle specification created successfully');
    }

    public function edit(VehicleSpec $vehicleSpec): Response
    {
        $vehicleSpec->load('items.assembly', 'items.material');
        $headers = VehicleSpecHeader::with(['items' => function($query) use ($vehicleSpec) {
            $query->where('vehicle_spec_id', $vehicleSpec->id)
                ->with(['assembly', 'material']);
        }])->get();
        return Inertia::render('VehicleSpec/Form', [
            'csrf' => csrf_token(),
            'spec' => $vehicleSpec,
            'headers' => $headers
        ]);
    }

    public function update(Request $request, VehicleSpec $vehicleSpec): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'name' => 'required',
            'groups.*.items.*.description' => 'nullable|string',
        ]);

        $vehicleSpec->update([
            'name' => $request->name,
        ]);

        VehicleSpecItem::where('vehicle_spec_id', $vehicleSpec->id)->delete();

        foreach ($request->groups as $header) {
            foreach ($header['items'] as $item) {
                $data = [
                    'vehicle_spec_id' => $vehicleSpec->id,
                    'header_id' => $header['header_id'],
                    'qty' => $item['qty'],
                    'remark' => $item['remark'],
                    'product_name' => $item['product_name'],
                    'description' => $item['description'],
                ];

                if ($item['item_id']) {
                    if ($item['type'] == 'Assembly Item') {
                        $data['assembly_id'] = $item['item_id'];
                    }
                    if ($item['type'] == 'Single Item') {
                        $data['product_id'] = $item['item_id'];
                    }
                }

                VehicleSpecItem::create($data);
            }
        }

        return redirect('/vehicle-spec')->with('updated', 'Vehicle specification updated successfully');
    }

    public function show(Request $request, VehicleSpec $vehicleSpec)
    {
        $headers = VehicleSpecHeader::with(['items' => function($query) use ($vehicleSpec) {
            $query->where('vehicle_spec_id', $vehicleSpec->id)
                ->with(['assembly', 'material']);
        }])->get();
        $vehicleSpec->load(['items.material.uom', 'items.assembly', 'items.header']);
        return Inertia::render('VehicleSpec/Detail', [
            'spec' => $vehicleSpec,
            'headers' => $headers
        ]);
    }

    public function destroy(Assembly $assembly)
    {
        $assembly->delete();

        return redirect('/assembly')->with('deleted', 'Assembly deleted successfully');
    }

    public function toggleStatus($assembly_id, $status)
    {
        $assembly = Assembly::withoutGlobalScope('active')->findOrFail($assembly_id);
        $assembly->update([
            'status' => $status,
        ]);
        return redirect()->back()->with('updated', 'Assembly updated successfully');
    }

    public function select2Query(Request $request)
    {
        if ($request->item_type == '') {
            return [];
        }
        if ($request->item_type == 'Assembly Item') {
            return Assembly::when($request->has('search'), function ($query) use ($request) {
                $query->where('code', 'LIKE', "%{$request->search}%")
                    ->orWhere('name', 'LIKE', "%{$request->search}%");
            })->with('product')->limit(10)->get();
        } else {
            return Product::with('uom')->when($request->has('search'), function ($query) use ($request) {
                $query->where('sku', 'LIKE', "%{$request->search}%")
                    ->orWhere('name', 'LIKE', "%{$request->search}%");
            })->with('prices')->limit(10)->get();
        }
    }

    public function select2QuerySpec(Request $request)
    {
        $specs = VehicleSpec::with(['items.material.uom', 'items.assembly.materials.product.uom', 'items.assembly.product.uom', 'items.header'])->when($request->has('search'), function ($query) use ($request) {
            $query->where('code', 'LIKE', "%{$request->search}%")
                ->orWhere('name', 'LIKE', "%{$request->search}%");
        })->limit(10)->get();
        return $specs;
    }

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

    public function replaceItem()
    {
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

    public function processReplaceItem(Request $request)
    {
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
            AssemblyMaterial::where([
                'assembly_id' => $assembly['id'],
                'product_id' => $request->existing_item_selected
            ])->update(['product_id' => $request->new_item_selected]);
        }

        return redirect('/assembly')->with('updated', 'Item replaced successfully');
    }

    public function duplicate(Assembly $assembly)
    {
        $assembly->load(['materials', 'processes']);
        $code = Assembly::generateCode();
        $newAssembly = Assembly::create([
            'code' => $code,
            'name' => $assembly->name,
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

    public function startProcess(Assembly $assembly, Process $process)
    {
        AssemblyProcess::where([
            'assembly_id' => $assembly->id,
            'process_id' => $process->id
        ])->update(['start_time' => date('Y-m-d H:i:s')]);

        return redirect()->back()->with('updated', 'Process started!');
    }
}
