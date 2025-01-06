<?php

namespace App\Http\Controllers;

use App\Models\StorageItem;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\ServiceMaintain;
use App\Models\ServiceOrder;
use App\Models\Country;
use App\Models\ServiceOrderRequirementUsed;
use App\Models\ServiceQuotation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class ServiceOrderController extends Controller
{
    public function select2Query(Request $request)
    {
        return ServiceOrder::with(['customer', 'vehicle'])
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where('id', 'LIKE', "%{$search}%")
                    ->whereHas('customer', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->limit(10)->get();
    }


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // Retrieve search keyword from request
        $search = $request->search;

        // Determine order and sort direction
        $order = $request->order ?? 'id';
        $by = $request->by ?? 'desc';

        // Determine pagination limit
        $paginate = $request->paginate ?? 10;

        // Filters to pass back to the view
        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        // Query to retrieve service orders with related entities
        $serviceOrders = ServiceOrder::with(['technician', 'customer', 'vehicle', 'picCountryPhoneCode', 'requirements.storageItem.product'])
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

        // Append query string parameters for pagination
        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];
        $serviceOrders->appends($queryString);

        // Return view with service orders and filters
        return Inertia::render('ServiceOrder/Index', [
            'csrf' => csrf_token(),
            'serviceOrders' => $serviceOrders,
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param ServiceOrder $service_order
     * @return Response|null
     */
    public function show(Request $request, ServiceOrder $service_order): ?Response
    {
        return (new ServiceOrderRequirementController())->index($request, $service_order);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        $countries = Country::all()->toArray();
        $phoneCodes = self::toSelect2Data($countries, 'id', 'phone_code');

        return Inertia::render('ServiceOrder/Form', [
            'csrf' => csrf_token(),
            'types' => ServiceOrder::TYPES,
            'phoneCodes' => $phoneCodes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate incoming request data
        $request->validate([
            'service_quotation_id' => 'nullable|exists:service_quotations,id',
            'technician_id' => 'nullable|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
            'confirmed_by' => 'nullable|string',
            'vehicle_id' => 'required|exists:vehicles,id',
            'pic_first_name' => 'nullable|string|max:255',
            'pic_last_name' => 'nullable|string|max:255',
            'pic_title' => 'nullable|string|max:255',
            'pic_country_code' => 'nullable|exists:countries,id',
            'pic_phone_number' => 'nullable|string|max:20',
            'pic_email' => 'nullable|email|max:255',
            'service_order_type' => 'nullable|string|max:255',
            'in_warranty' => 'required|boolean',
            'vehicle_parts' => 'required|array',
            'vehicle_parts.*.storage_item_id' => 'required|exists:storage_items,id',
            'vehicle_parts.*.quantity' => 'required|integer|min:1',
            'remark' => 'nullable|string',
            'status' => 'required|integer|in:1,2',
            'plan_start_date' => 'nullable|date',
            'plan_complete_date' => 'nullable|date',
            'date_required' => 'required|date|after_or_equal:' . now()->toDateString(),
        ], [
            // Custom validation error messages
            'service_quotation_id.required' => 'Service Quotation ID is required.',
            'service_quotation_id.exists' => 'The selected Service Quotation ID does not exist.',
            'technician_id.required' => 'Technician ID is required.',
            'technician_id.exists' => 'The selected Technician ID does not exist.',
            'customer_id.required' => 'Customer ID is required.',
            'customer_id.exists' => 'The selected Customer ID does not exist.',
            'vehicle_id.required' => 'Vehicle ID is required.',
            'vehicle_id.exists' => 'The selected Vehicle ID does not exist.',
            'pic_first_name.required' => 'PIC First Name is required.',
            'pic_last_name.required' => 'PIC Last Name is required.',
            'pic_title.max' => 'PIC Title may not be greater than 255 characters.',
            'pic_country_code.required' => 'PIC Country Code is required.',
            'pic_country_code.max' => 'PIC Country Code may not be greater than 10 characters.',
            'pic_phone_number.required' => 'PIC Phone Number is required.',
            'pic_phone_number.max' => 'PIC Phone Number may not be greater than 20 characters.',
            'pic_email.required' => 'PIC Email is required.',
            'pic_email.email' => 'PIC Email must be a valid email address.',
            'pic_email.max' => 'PIC Email may not be greater than 255 characters.',
            'service_order_type.required' => 'Service Order Type is required.',
            'service_order_type.max' => 'Service Order Type may not be greater than 255 characters.',
            'in_warranty.required' => 'Warranty status is required.',
            'in_warranty.boolean' => 'Warranty status must be true or false.',
            'vehicle_parts.*.storage_item_id.required' => 'The selected Storage Item ID is required.',
            'vehicle_parts.*.storage_item_id.exists' => 'The selected Storage Item ID does not exist.',
            'vehicle_parts.*.quantity.required' => 'Each vehicle part must have a quantity.',
            'vehicle_parts.*.quantity.integer' => 'Vehicle part quantity must be an integer.',
            'vehicle_parts.*.quantity.min' => 'Vehicle part quantity must be at least 1.',
            'remark.max' => 'Remark may not be greater than 255 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either 1 (active) or 2 (draft).',
        ]);

        // Extract validated data
        $data = $request->all();

        // Check if this is a "Come Back Job"
        $vehicleId = $request->vehicle_id;

        $lastServiceOrder = ServiceOrder::where('vehicle_id', $vehicleId)
            ->orderBy('delivery_date', 'desc')
            ->first();

        if ($lastServiceOrder && Carbon::parse($lastServiceOrder->delivery_date)->greaterThanOrEqualTo(Carbon::now()->subDays(7))) {
            $data['come_back_job'] = true;
        } else {
            $data['come_back_job'] = false;
        }

        // Check service_order_type for CMP
        if (strtolower($request->service_order_type) === 'cmp') {
            $data['in_cmp'] = true;
        } else {
            $data['in_cmp'] = false;
        }

        // Find or determine the appropriate ServiceMaintain record for the vehicle and the current or future month
        $vehicleId = $request->vehicle_id;
        $currentMonth = Carbon::now()->startOfMonth();
        $serviceMaintain = ServiceMaintain::where('vehicle_id', $vehicleId)
            ->whereBetween('date', [$currentMonth, $currentMonth->copy()->endOfMonth()])
            ->first();

        if (!$serviceMaintain) {
            // If no maintenance found for the current month, look for future maintenance
            $serviceMaintain = ServiceMaintain::where('vehicle_id', $vehicleId)
                ->where('date', '>', $currentMonth->endOfMonth())
                ->orderBy('date', 'asc')
                ->first();
        }

        if ($serviceMaintain) {
            $data['service_maintain_id'] = $serviceMaintain->id;
        } else {
            return back()->with('deleted', 'No maintenance service found for this vehicle!');
        }

        // Convert dates to Carbon instances
        $data['plan_start_date'] = Carbon::parse($data['plan_start_date']);
        $data['plan_complete_date'] = Carbon::parse($data['plan_complete_date']);

        // Create a new ServiceOrder record with the validated data
        $order = ServiceOrder::create($data);

        // Generate a unique code for the ServiceOrder based on its status
        $order->update(['code' => ($order->status == ServiceOrder::STATUS_DRAFT ? ServiceOrder::DRAFT_PREFIX : ServiceOrder::ACTIVE_PREFIX) . str_pad($order->id, 4, '0', STR_PAD_LEFT)]);

        // Create a new Requisition record
        $requisitionData = [
            'status' => Requisition::PENDING_STATUS,
            'type' => Requisition::SERVICE_ORDER_TYPE,
            'service_order_id' => $order->id,
            'requested_by' => auth()->id(),
            'date' => Carbon::parse($request->date_required),
            'note' => 'Generated from Service Order ID ' . $order->id,
        ];
        $requisition = Requisition::create($requisitionData);
        $requisition->update(['code' => 'PR' . str_pad($requisition->id, 4, '0', STR_PAD_LEFT)]);

        foreach ($request->vehicle_parts as $part) {
            $storage_item = StorageItem::find($part['storage_item_id'])->load('product');

            if ($storage_item && $part['quantity']) {
                // Create a new VehiclePart entry associated with the order
                $order->vehicleParts()->create([
                    'name' => $storage_item->product ? $storage_item->product->name : '-',
                    'storage_item_id' => $part['storage_item_id'],
                    'quantity' => $part['quantity'],
                ]);

                // Create a new ServiceOrderRequirement entry
                $requirement = $order->requirements()->create([
                    'storage_item_id' => $part['storage_item_id'],
                    'quantity' => $part['quantity'],
                ]);

                // Create RequisitionItem entries for each vehicle part
                $requisition_item = RequisitionItem::create([
                    'product_id' => $storage_item->product?->id,
                    'status' => Requisition::PENDING_STATUS,
                    'requisition_id' => $requisition->id,
                    'product_name' => $storage_item->product?->name,
                    'quantity' => $part['quantity'],
                    'date' => Carbon::parse($request->date_required),
                    'remark' => 'Generated from Service Order ID ' . $order->id,
                    'isRelease' => false,
                ]);

                // Create a corresponding ServiceOrderRequirementUsed entry
                ServiceOrderRequirementUsed::create([
                    'service_order_id' => $order->id,
                    'service_order_requirement_id' => $requirement->id,
                    'requisition_id' => $requisition->id,
                    'requisition_item_id' => $requisition_item->id,
                    'quantity' => $part['quantity'],
                ]);
            }
        }

        if ($order->customer_feedback) {
            $order->reports()->create([
                'name' => 'Customer Feedback',
                'remarks' => $order->customer_feedback,
            ]);
        }

        // Redirect with a success message
        return redirect('/service-orders')->with('created', 'Service order created successfully.');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param ServiceOrder $service_order
     * @return Response|null
     */

    public function edit(ServiceOrder $service_order): ?Response
    {
        if ($service_order->total_process != 0) return null;

        // Load all necessary relationships
        $service_order->load('vehicleParts.storageItem', 'serviceQuotation', 'technician', 'customer', 'vehicle');

        // Fetch countries and prepare phone codes for Select2
        $countries = Country::all()->toArray();
        $phoneCodes = self::toSelect2Data($countries, 'id', 'phone_code');

        // Prepare data for Select2 components
        $serviceQuotations = $this->toSelect2Data([$service_order->serviceQuotation], 'id', 'id');
        $technicians = $this->toSelect2Data([$service_order->technician], 'id', 'name');
        $customers = $this->toSelect2Data([$service_order->customer], 'id', 'code', 'name', ' | ');
        $vehicles = $this->toSelect2Data([$service_order->vehicle], 'id', 'license_plate');

        $storage_items = $service_order->vehicleParts->pluck('storageItem')->flatten()->map(function ($item) {
            if (!empty($item)) {
                return [
                    'id' => $item->id,
                    'text' => $item->storage->code . ' - ' . $item->product->sku . ' | ' . $item->product->name
                ];
            }
        });

        // Return the Inertia response with the required data
        return Inertia::render('ServiceOrder/Form', [
            'csrf' => csrf_token(),
            'order' => $service_order,
            'types' => ServiceOrder::TYPES,
            'phoneCodes' => $phoneCodes,
            'serviceQuotations' => $serviceQuotations,
            'technicians' => $technicians,
            'customer' => $service_order->customer,
            'customers' => $customers,
            'vehicle' => $service_order->vehicle,
            'vehicles' => $vehicles,
            'storage_items' => $storage_items,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ServiceOrder $service_order
     * @return RedirectResponse
     */
    public function update(Request $request, ServiceOrder $service_order): RedirectResponse
    {
        // Validation rules with custom messages
        $request->validate([
            'service_quotation_id' => 'nullable|exists:service_quotations,id',
            'technician_id' => 'nullable|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'pic_first_name' => 'nullable|string|max:255',
            'pic_last_name' => 'nullable|string|max:255',
            'pic_title' => 'nullable|string|max:255',
            'pic_country_code' => 'nullable|string|max:10',
            'pic_phone_number' => 'nullable|string|max:20',
            'pic_email' => 'nullable|email|max:255',
            'service_order_type' => 'nullable|string|max:255',
            'in_warranty' => 'required|boolean',
            'vehicle_parts' => 'required|array',
            'vehicle_parts.*.storage_item_id' => 'required|exists:storage_items,id',
            'vehicle_parts.*.quantity' => 'required|integer|min:1',
            'remark' => 'nullable|string',
            'status' => 'required|integer|in:1,2',
        ], [
            'service_quotation_id.required' => 'Service Quotation ID is required.',
            'service_quotation_id.exists' => 'The selected Service Quotation ID does not exist.',
            'technician_id.required' => 'Technician ID is required.',
            'technician_id.exists' => 'The selected Technician ID does not exist.',
            'customer_id.required' => 'Customer ID is required.',
            'customer_id.exists' => 'The selected Customer ID does not exist.',
            'vehicle_id.required' => 'Vehicle ID is required.',
            'vehicle_id.exists' => 'The selected Vehicle ID does not exist.',
            'pic_first_name.required' => 'PIC First Name is required.',
            'pic_last_name.required' => 'PIC Last Name is required.',
            'pic_title.max' => 'PIC Title may not be greater than 255 characters.',
            'pic_country_code.required' => 'PIC Country Code is required.',
            'pic_country_code.max' => 'PIC Country Code may not be greater than 10 characters.',
            'pic_phone_number.required' => 'PIC Phone Number is required.',
            'pic_phone_number.max' => 'PIC Phone Number may not be greater than 20 characters.',
            'pic_email.required' => 'PIC Email is required.',
            'pic_email.email' => 'PIC Email must be a valid email address.',
            'pic_email.max' => 'PIC Email may not be greater than 255 characters.',
            'service_order_type.required' => 'Service Order Type is required.',
            'service_order_type.max' => 'Service Order Type may not be greater than 255 characters.',
            'in_warranty.required' => 'Warranty status is required.',
            'in_warranty.boolean' => 'Warranty status must be true or false.',
            'vehicle_parts.*.storage_item_id.required' => 'The selected Storage Item ID is required.',
            'vehicle_parts.*.storage_item_id.exists' => 'The selected Storage Item ID does not exist.',
            'vehicle_parts.*.quantity.required' => 'Each vehicle part must have a quantity.',
            'vehicle_parts.*.quantity.integer' => 'Vehicle part quantity must be an integer.',
            'vehicle_parts.*.quantity.min' => 'Vehicle part quantity must be at least 1.',
            'remark.max' => 'Remark may not be greater than 255 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either 1 (active) or 2 (draft).',
        ]);

        $data = $request->all();

        // Update the service order
        $service_order->update($data);

        // Sync vehicle parts
        $service_order->vehicleParts()->delete();
        foreach ($request->vehicle_parts as $part) {
            $storage_item = StorageItem::find($part['storage_item_id'])->load('product');

            if ($storage_item && $part['quantity']) {
                $service_order->vehicleParts()->create([
                    'name' => $storage_item->product ? $storage_item->product->name : '-',
                    'storage_item_id' => $part['storage_item_id'],
                    'quantity' => $part['quantity'],
                ]);
            }
        }

        // Redirect with a success message
        return redirect('/service-orders')->with('updated', 'Service order updated successfully.');
    }

    /**
     * Update the specified resource from storage.
     *
     * @param Request $request
     * @param ServiceOrder $service_order
     * @return RedirectResponse
     */
    public function updateDetail(Request $request, ServiceOrder $service_order)
    {
        // Validate input data
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'confirmed_by' => 'nullable|string',
            'service_quotation_id' => 'nullable|exists:service_quotations,id',
            'plan_start_date' => 'nullable|date',
            'plan_complete_date' => 'nullable|date|after_or_equal:plan_start_date',
            'come_back_job' => 'nullable|boolean',
            'in_warranty' => 'nullable|boolean',
            'in_cmp' => 'nullable|boolean',
            'service_order_type' => 'nullable|string|max:255',
            'warranty_sale_order_eco_id' => 'nullable|exists:sales_orders_eco,id',
            'warranty_contract_id' => 'nullable|exists:service_contracts,id',
        ]);

        // Check if in_cmp is true (1)
        if (isset($validated['in_cmp']) && $validated['in_cmp']) {
            // Get the vehicle associated with the service order
            $vehicle = $service_order->vehicle;

            // Check if the vehicle exists
            if (!$vehicle) {
                return back()->withErrors(['in_cmp' => 'Vehicle not found for this Service Order.']);
            }

            // Get current date
            $today = Carbon::today();

            // Check if the vehicle has any valid service contracts
            $hasValidContract = $vehicle->serviceContracts()
                ->where('start_duration_date', '<=', $today)
                ->where('end_duration_date', '>=', $today)
                ->exists();

            if (!$hasValidContract) {
                return back()->withErrors(['in_cmp' => 'Cannot select CMP as Yes because the vehicle does not have a valid CMP.']);
            }
        }

        // The rest of your existing code...

        // Check if service_quotation_id is present
        if (!empty($validated['service_quotation_id'])) {
            $serviceQuotation = ServiceQuotation::find($validated['service_quotation_id']);

            // Check if customer_id and vehicle_id match with the service order
            if (intval($serviceQuotation->customer_id) !== intval($validated['customer_id'])) {
                return back()->with('deleted', 'Customer ID or Vehicle ID does not match the Service Quotation.')
                    ->withErrors([
                        'customer_id' => 'Customer ID does not match the Service Quotation.',
                        'service_quotation_id' => 'Customer ID does not match the Service Quotation.',
                    ]);
            }
            if ($serviceQuotation->vehicle_id !== $service_order->vehicle_id) {
                return back()->with('deleted', 'Vehicle ID does not match the Service Quotation.')
                    ->withErrors([
                        'service_quotation_id' => 'Vehicle ID does not match the Service Quotation.',
                    ]);
            }

            // Update service_order_id in the Service Quotation
            $serviceQuotation->update(['service_order_id' => $service_order->id]);

            // Update ServiceOrder requirements
            $this->updateServiceOrderRequirements($serviceQuotation, $service_order);
        }

        if (isset($validated['plan_start_date'])) {
            $validated['plan_start_date'] = Carbon::parse($validated['plan_start_date']);
        }
        if (isset($validated['plan_complete_date'])) {
            $validated['plan_complete_date'] = Carbon::parse($validated['plan_complete_date']);
        }
        if (isset($validated['est_complete_date'])) {
            $validated['est_complete_date'] = Carbon::parse($validated['est_complete_date']);
        }

        // Update the Service Order with validated data
        $service_order->update($validated);

        // Redirect back with success message
        return back()->with('updated', 'Service appointment updated successfully.');
    }

    protected function updateServiceOrderRequirements(ServiceQuotation $quotation, ServiceOrder $serviceOrder)
    {
        // Get existing requirements' storage_item_ids
        $existingRequirements = $serviceOrder->requirements->pluck('storage_item_id')->toArray();

        // For each vehiclePart in quotation
        foreach ($quotation->vehicleParts as $vehiclePart) {
            // Check if storage_item_id exists in requirements
            if (!in_array($vehiclePart->storage_item_id, $existingRequirements)) {
                // Create new requirement
                $serviceOrder->requirements()->create([
                    'storage_item_id' => $vehiclePart->storage_item_id,
                    'quantity' => $vehiclePart->quantity,
                ]);
            }
        }
    }


    /**
     * Update the specified resource from storage.
     *
     * @param ServiceOrder $service_order
     * @return RedirectResponse
     */
    public function startService(ServiceOrder $service_order): RedirectResponse
    {
        $service_order->update(['status' => ServiceOrder::STATUS_FIRST_CHECK]);

        // Update code
        $service_order->update([
            'code' =>
                ($service_order->status == ServiceOrder::STATUS_DRAFT ? ServiceOrder::DRAFT_PREFIX : ServiceOrder::ACTIVE_PREFIX)
                . str_pad($service_order->id, 4, '0', STR_PAD_LEFT)
        ]);


        // Redirect back with success message
        return redirect('/service-orders')->with('updated', 'Service appointment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ServiceOrder $service_order
     * @return RedirectResponse
     */
    public function destroy(ServiceOrder $service_order): RedirectResponse
    {
        // Delete the service order
        $service_order->update(['status' => ServiceOrder::STATUS_VOID]);

        // Redirect back with success message
        return redirect('/service-orders')->with('deleted', 'Service order deleted successfully.');
    }
}
