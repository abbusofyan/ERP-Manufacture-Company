<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\ServiceAppointment;
use App\Models\ServiceOrder;
use App\Models\ServiceOrderProcess;
use App\Models\ServiceOrderRequirement;
use App\Models\ServiceOrderRequirementUsed;
use App\Models\ServiceQuotation;
use App\Models\ServiceQuotationVehiclePart;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceOrderRequirementUsedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param ServiceOrder $service_order
     * @return Response
     */
    public function index(Request $request, ServiceOrder $service_order): Response
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

        // Query to retrieve service order requirements used with related entities
        $requirementsUsed = ServiceOrderRequirementUsed::where('service_order_id', $service_order->id)
            ->with(['requirement', 'requirement.storageItem.storage', 'requirement.storageItem.product.uom', 'requirement.storageItem.product.category', 'requisition', 'requisitionItem'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('requirement.storageItem.product', function ($query) use ($search) {
                    $query->where('sku', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%");
                });
            })
            ->when($order, function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })
            ->paginate($paginate);

        // Append query string parameters for pagination
        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];
        $requirementsUsed->appends($queryString);

        return Inertia::render('ServiceOrder/RequirementUsed/View', [
            'csrf' => csrf_token(),
            'stages' => ServiceOrderProcess::STAGES,
            'types' => ServiceAppointment::TYPES,
            'serviceOrder' => $service_order->load(['serviceQuotation', 'technician', 'customer', 'vehicle.salesOrders.refrigerationSale', 'currentProcess']),
            'requirementsUsed' => $requirementsUsed,
            'filters' => $filters,
        ]);
    }


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param ServiceOrder $service_order
     * @return Response
     */
    public function showRequisitions(Request $request, ServiceOrder $service_order): Response
    {
        return Inertia::render('ServiceOrder/RequirementUsed/Requisitions', [
            'csrf' => csrf_token(),
            'stages' => ServiceOrderProcess::STAGES,
            'types' => ServiceAppointment::TYPES,
            'serviceOrder' => $service_order->load(['usedRequirements.requisitionItem.requisition', 'usedRequirements.requisitionItem.product']),
        ]);
    }


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param ServiceOrder $service_order
     * @return Response
     */
    public function showRequisitionStockStatus(Request $request, ServiceOrder $service_order): Response
    {
        return Inertia::render('ServiceOrder/RequirementUsed/LiveStock', [
            'csrf' => csrf_token(),
            'stages' => ServiceOrderProcess::STAGES,
            'types' => ServiceAppointment::TYPES,
            'serviceOrder' => $service_order->load(['usedRequirements.requisitionItem.requisition', 'usedRequirements.requisitionItem.product']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param ServiceOrder $service_order
     * @return RedirectResponse
     */
    public function store(Request $request, ServiceOrder $service_order)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'service_order_requirement_id' => 'required|exists:service_order_requirements,id',
            'quantity' => 'required|integer|min:1',
            'date_required' => 'required|date|after_or_equal:' . now()->toDateString(),
        ]);

        $requirement = ServiceOrderRequirement::with('storageItem')->findOrFail($validated['service_order_requirement_id']);
        $max_qty = $requirement->storageItem->quantity;

        // Validate quantity
        if ($validated['quantity'] > $max_qty) {
            return redirect()->back()->withErrors(['quantity' => "Quantity exceeds available quantity (<= $max_qty)."]);
        }

        // Get or create requisition
        $requisition = Requisition::firstOrCreate(
            [
                'service_order_id' => $service_order->id,
                'status' => Requisition::PENDING_STATUS,
                'requested_by' => auth()->id(),
            ],
            [
                'type' => Requisition::SERVICE_ORDER_TYPE,
                'date' => Carbon::parse($validated['date_required']),
                'note' => 'Generated from Service Order ID ' . $service_order->id,
            ]
        );

        $requisitionItem = RequisitionItem::create([
            'product_id' => $requirement->storageItem->product?->id,
            'status' => Requisition::PENDING_STATUS,
            'requisition_id' => $requisition->id,
            'product_name' => $requirement->storageItem->product?->name,
            'requested_qty' => $validated['quantity'],
            'pending_order_qty' => $validated['quantity'],
            'date' => Carbon::parse($validated['date_required']),
            'remark' => 'Generated from Service Order ID ' . $service_order->id,
            'isRelease' => false,
        ]);

        ServiceOrderRequirementUsed::create([
            'service_order_requirement_id' => $requirement->id,
            'service_order_id' => $service_order->id,
            'requisition_id' => $requisition->id,
            'requisition_item_id' => $requisitionItem->id,
            'quantity' => $requisitionItem['pending_order_qty'],
        ]);

        $service_order->update(['quotation_generated_after_update' => false]);

        // Redirect with a success message
        return redirect()->back()->with('success', 'Requirement used added/updated successfully.');
    }


    /**
     * Convert the specified resource from storage.
     *
     * @param ServiceOrder $service_order
     * @return RedirectResponse
     */
    public function generateQuotation(ServiceOrder $service_order): RedirectResponse
    {
        // Check if the service order already has a quotation
        if ($service_order->service_quotation_id !== null) {
            $quotation = ServiceQuotation::find($service_order->service_quotation_id);
        } else {
            $quotation = ServiceQuotation::create([
                'service_order_id' => $service_order->id,
                'customer_id' => $service_order->customer_id,
                'vehicle_id' => $service_order->vehicle_id,
                'pic_first_name' => $service_order->pic_first_name,
                'pic_last_name' => $service_order->pic_last_name,
                'pic_title' => $service_order->pic_title,
                'pic_country_code' => $service_order->pic_country_code,
                'pic_phone_number' => $service_order->pic_phone_number,
                'pic_email' => $service_order->pic_email,
                'service_order_type' => $service_order->service_order_type,
                'service_order_value' => 0, // Initial value
                'remark' => $service_order->remark,
                'sub_total' => 0,
                'discount' => 0,
                'gst_rate' => 0,
                'gst_total' => 0,
                'total' => 0,
                'status' => ServiceQuotation::STATUS_PENDING,
            ]);
            $quotation->update(['code' => ServiceQuotation::PREFIX . str_pad($quotation->id, 4, '0', STR_PAD_LEFT)]);

            // Update the service order with the new quotation ID
            $service_order->update([
                'service_quotation_id' => $quotation->id,
            ]);
        }

        //Loop all requirement used
        foreach ($service_order->usedRequirements as $usedRequirement) {
            if ($usedRequirement->service_quotation_id && $usedRequirement->service_quotation_vehicle_part_id) {
                //Requirement used has quotation already
                $usedRequirement->serviceQuotationVehiclePart->update(['quantity' => $usedRequirement->quantity]);
            } else {
                //Requirement used hasn't quotation vehicle pass
                $newPart = ServiceQuotationVehiclePart::create([
                    'service_quotation_id' => $quotation->id,
                    'storage_item_id' => $usedRequirement->requirement->storageItem->id,
                    'name' => $usedRequirement->requirement->storageItem->product->name,
                    'quantity' => $usedRequirement->quantity,
                ]);

                $usedRequirement->update([
                    'service_quotation_id' => $quotation->id,
                    'service_quotation_vehicle_part_id' => $newPart->id,
                ]);
            }
        }

        $service_order->update(['quotation_generated_after_update' => false]);

        // Redirect back with success message
        return back()->with('updated', 'Quotation created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ServiceOrder $service_order
     * @param ServiceOrderRequirementUsed $requirementUsed
     * @return RedirectResponse
     */
    public function destroy(ServiceOrder $service_order, ServiceOrderRequirementUsed $requirementUsed): RedirectResponse
    {
        // Delete the service order requirement used
        $requirementUsed->delete();

        // Redirect back with success message
        return redirect("/service-orders/$service_order->id")->with('deleted', 'Service order requirement used deleted successfully.');
    }
}

