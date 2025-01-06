<?php

namespace App\Http\Controllers;

use App\Models\ProjectOrder;
use App\Models\ProjectQuotation;
use App\Models\StorageItem;
use App\Models\Country;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\ProjectOrderRequirementUsed;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectOrderController extends Controller
{
    public function select2Query(Request $request)
    {
        return ProjectOrder::with(['customer', 'vehicle'])
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where('id', 'LIKE', "%{$search}%")
                    ->whereHas('customer', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->limit(10)->get();
    }

    public function index(Request $request): Response
    {
        $search = $request->search;
        $order = $request->order ?? 'id';
        $by = $request->by ?? 'desc';
        $paginate = $request->paginate ?? 10;

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $projectOrders = ProjectOrder::with(['technician', 'customer', 'vehicles', 'requirements.storageItem.product'])
            ->when($search, function ($query) use ($search) {
                $query
                    ->where('id', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('requirements.storageItem.product', function ($query) use ($search) {
                        $query->where('sku', 'LIKE', "%{$search}%")
                            ->orWhere('name', 'LIKE', "%{$search}%");
                    });
            })
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        $projectOrders->appends([
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ]);

        return Inertia::render('ProjectOrder/Index', [
            'csrf' => csrf_token(),
            'projectOrders' => $projectOrders,
            'filters' => $filters,
        ]);
    }

    public function show(Request $request, ProjectOrder $project_order): ?Response
    {
        return (new ProjectOrderRequirementController())->index($request, $project_order);
    }

    public function create(): Response
    {
        $countries = Country::all()->toArray();
        $phoneCodes = self::toSelect2Data($countries, 'id', 'phone_code');

        return Inertia::render('ProjectOrder/Form', [
            'csrf' => csrf_token(),
            'types' => ProjectOrder::TYPES,
            'phoneCodes' => $phoneCodes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'project_quotation_id' => 'nullable|exists:project_quotations,id',
            'technician_id' => 'nullable|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
            'vehicle_ids' => 'required|array',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'pic_first_name' => 'nullable|string|max:255',
            'pic_last_name' => 'nullable|string|max:255',
            'pic_title' => 'nullable|string|max:255',
            'pic_country_code' => 'nullable|exists:countries,id',
            'pic_phone_number' => 'nullable|string|max:20',
            'pic_email' => 'nullable|email|max:255',
            'project_order_type' => 'nullable|string|max:255',
            'in_warranty' => 'required|boolean',
            'vehicle_parts' => 'required|array',
            'vehicle_parts.*.storage_item_id' => 'required|exists:storage_items,id',
            'vehicle_parts.*.quantity' => 'required|integer|min:1',
            'remark' => 'nullable|string',
            'status' => 'required|integer|in:1,2',
            'plan_start_date' => 'nullable|date',
            'plan_complete_date' => 'nullable|date',
            'date_required' => 'required|date|after_or_equal:' . now()->toDateString(),
        ]);

        $data = $request->all();
        $data['plan_start_date'] = Carbon::parse($data['plan_start_date']);
        $data['plan_complete_date'] = Carbon::parse($data['plan_complete_date']);

        $order = ProjectOrder::create($data);

        $order->update(['code' => ($order->status == ProjectOrder::STATUS_DRAFT ? ProjectOrder::DRAFT_PREFIX : ProjectOrder::ACTIVE_PREFIX) . str_pad($order->id, 4, '0', STR_PAD_LEFT)]);

        // Attach vehicles to the project contract
        $order->vehicles()->attach($data['vehicle_ids']);

        // Create requisition
        $requisition = Requisition::create([
            'status' => Requisition::PENDING_STATUS,
            'type' => Requisition::PROJECT_ORDER,
            'project_order_id' => $order->id,
            'requested_by' => auth()->id(),
            'date' => Carbon::parse($request->date_required),
            'note' => 'Generated from Project Order ID ' . $order->id,
        ]);

        $requisition->update(['code' => 'PR' . str_pad($requisition->id, 4, '0', STR_PAD_LEFT)]);

        foreach ($request->vehicle_parts as $part) {
            $storage_item = StorageItem::find($part['storage_item_id'])->load('product');

            if ($storage_item && $part['quantity']) {
                $order->vehicleParts()->create([
                    'name' => $storage_item->product ? $storage_item->product->name : '-',
                    'storage_item_id' => $part['storage_item_id'],
                    'quantity' => $part['quantity'],
                ]);

                $requirement = $order->requirements()->create([
                    'storage_item_id' => $part['storage_item_id'],
                    'quantity' => $part['quantity'],
                ]);

                $requisition_item = RequisitionItem::create([
                    'product_id' => $storage_item->product?->id,
                    'status' => Requisition::PENDING_STATUS,
                    'requisition_id' => $requisition->id,
                    'product_name' => $storage_item->product?->name,
                    'quantity' => $part['quantity'],
                    'date' => Carbon::parse($request->date_required),
                    'remark' => 'Generated from Project Order ID ' . $order->id,
                    'isRelease' => false,
                ]);

                ProjectOrderRequirementUsed::create([
                    'project_order_id' => $order->id,
                    'project_order_requirement_id' => $requirement->id,
                    'requisition_id' => $requisition->id,
                    'requisition_item_id' => $requisition_item->id,
                    'quantity' => $part['quantity'],
                ]);
            }
        }

        return redirect('/project-orders')->with('created', 'Project order created successfully.');
    }

    public function edit(ProjectOrder $project_order): ?Response
    {
        if ($project_order->total_process != 0) return null;

        $project_order->load('vehicleParts.storageItem', 'projectQuotation', 'technician', 'customer', 'vehicle');

        $countries = Country::all()->toArray();
        $phoneCodes = self::toSelect2Data($countries, 'id', 'phone_code');

        $projectQuotations = $this->toSelect2Data([$project_order->projectQuotation], 'id', 'id');
        $technicians = $this->toSelect2Data([$project_order->technician], 'id', 'name');
        $customers = $this->toSelect2Data([$project_order->customer], 'id', 'code', 'name', ' | ');
        $vehicles = $this->toSelect2Data($project_order->vehicles, 'id', 'license_plate');
        $vehicles_select = $vehicles->map(function ($vehicle) {
            return [
                'id' => $vehicle->id,
                'text' => $vehicle->license_plate,
                'data' => $vehicle->toArray()
            ];
        });

        $storage_items = $project_order->vehicleParts->pluck('storageItem')->flatten()->map(function ($item) {
            if (!empty($item)) {
                return [
                    'id' => $item->id,
                    'text' => $item->storage->code . ' - ' . $item->product->sku . ' | ' . $item->product->name
                ];
            }
        });

        return Inertia::render('ProjectOrder/Form', [
            'csrf' => csrf_token(),
            'order' => $project_order,
            'types' => ProjectOrder::TYPES,
            'phoneCodes' => $phoneCodes,
            'projectQuotations' => $projectQuotations,
            'technicians' => $technicians,
            'customer' => $project_order->customer,
            'customers' => $customers,
            'vehicles' => $vehicles,
            'vehicles_select' => $vehicles_select,
            'storage_items' => $storage_items,
        ]);
    }

    public function update(Request $request, ProjectOrder $project_order): RedirectResponse
    {
        $request->validate([
            'project_quotation_id' => 'nullable|exists:project_quotations,id',
            'technician_id' => 'nullable|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
            'vehicle_ids' => 'required|array',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'pic_first_name' => 'nullable|string|max:255',
            'pic_last_name' => 'nullable|string|max:255',
            'pic_title' => 'nullable|string|max:255',
            'pic_country_code' => 'nullable|string|max:10',
            'pic_phone_number' => 'nullable|string|max:20',
            'pic_email' => 'nullable|email|max:255',
            'project_order_type' => 'nullable|string|max:255',
            'in_warranty' => 'required|boolean',
            'vehicle_parts' => 'required|array',
            'vehicle_parts.*.storage_item_id' => 'required|exists:storage_items,id',
            'vehicle_parts.*.quantity' => 'required|integer|min:1',
            'remark' => 'nullable|string',
            'status' => 'required|integer|in:1,2',
        ]);

        $data = $request->all();

        $project_order->update($data);

        // Sync vehicles with the project contract
        $project_order->vehicles()->sync($data['vehicle_ids']);

        $project_order->vehicleParts()->delete();
        foreach ($request->vehicle_parts as $part) {
            $storage_item = StorageItem::find($part['storage_item_id'])->load('product');

            if ($storage_item && $part['quantity']) {
                $project_order->vehicleParts()->create([
                    'name' => $storage_item->product ? $storage_item->product->name : '-',
                    'storage_item_id' => $part['storage_item_id'],
                    'quantity' => $part['quantity'],
                ]);
            }
        }

        return redirect('/project-orders')->with('updated', 'Project order updated successfully.');
    }

    public function updateDetail(Request $request, ProjectOrder $project_order)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'confirmed_user_id' => 'nullable|exists:users,id',
            'project_quotation_id' => 'nullable|exists:project_quotations,id',
            'plan_start_date' => 'nullable|date',
            'plan_complete_date' => 'nullable|date|after_or_equal:plan_start_date',
        ]);

        if (!empty($validated['project_quotation_id'])) {
            $projectQuotation = ProjectQuotation::find($validated['project_quotation_id']);

            if (intval($projectQuotation->customer_id) !== intval($validated['customer_id'])) {
                return back()->with('deleted', 'Customer ID or Vehicle ID does not match the Project Quotation.')
                    ->withErrors([
                        'customer_id' => 'Customer ID does not match the Project Quotation.',
                        'project_quotation_id' => 'Customer ID does not match the Project Quotation.',
                    ]);
            }
            if ($projectQuotation->vehicle_id !== $project_order->vehicle_id) {
                return back()->with('deleted', 'Vehicle ID does not match the Project Quotation.')
                    ->withErrors([
                        'project_quotation_id' => 'Vehicle ID does not match the Project Quotation.',
                    ]);
            }

            $projectQuotation->update(['project_order_id' => $project_order->id]);
        }

        if (isset($validated['plan_start_date'])) {
            $validated['plan_start_date'] = Carbon::parse($validated['plan_start_date']);
        }
        if (isset($validated['plan_complete_date'])) {
            $validated['plan_complete_date'] = Carbon::parse($validated['plan_complete_date']);
        }

        $project_order->update($validated);

        return back()->with('updated', 'Project order updated successfully.');
    }

    public function startProject(ProjectOrder $project_order): RedirectResponse
    {
        $project_order->update(['status' => ProjectOrder::STATUS_FIRST_CHECK]);

        $project_order->update([
            'code' =>
                ($project_order->status == ProjectOrder::STATUS_DRAFT ? ProjectOrder::DRAFT_PREFIX : ProjectOrder::ACTIVE_PREFIX)
                . str_pad($project_order->id, 4, '0', STR_PAD_LEFT)
        ]);

        return redirect('/project-orders')->with('updated', 'Project order updated successfully.');
    }

    public function destroy(ProjectOrder $project_order): RedirectResponse
    {
        $project_order->update(['status' => ProjectOrder::STATUS_VOID]);

        return redirect('/project-orders')->with('deleted', 'Project order deleted successfully.');
    }
}
