<?php

namespace App\Http\Controllers;

use App\Models\ProjectOrder;
use App\Models\ProjectOrderRequirementUsed;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\ProjectOrderRequirement;
use App\Models\ProjectQuotation;
use App\Models\ProjectQuotationVehiclePart;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectOrderRequirementUsedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param ProjectOrder $project_order
     * @return Response
     */
    public function index(Request $request, ProjectOrder $project_order): Response
    {
        $search = $request->search;
        $order = $request->order ?? 'id';
        $by = $request->by ?? 'desc';
        $paginate = $request->paginate ?? 10;

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $requirementsUsed = ProjectOrderRequirementUsed::where('project_order_id', $project_order->id)
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

        $requirementsUsed->appends([
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ]);

        return Inertia::render('ProjectOrder/RequirementUsed/View', [
            'csrf' => csrf_token(),
            'projectOrder' => $project_order->load(['projectQuotation', 'technician', 'customer', 'vehicles']),
            'requirementsUsed' => $requirementsUsed,
            'filters' => $filters,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param ProjectOrder $project_order
     * @return RedirectResponse
     */
    public function store(Request $request, ProjectOrder $project_order): RedirectResponse
    {
        $validated = $request->validate([
            'project_order_requirement_id' => 'required|exists:project_order_requirements,id',
            'quantity' => 'required|integer|min:1',
            'date_required' => 'required|date|after_or_equal:' . now()->toDateString(),
        ]);

        $requirement = ProjectOrderRequirement::with('storageItem')->findOrFail($validated['project_order_requirement_id']);
        $max_qty = $requirement->storageItem->quantity;

        if ($validated['quantity'] > $max_qty) {
            return redirect()->back()->withErrors(['quantity' => "Quantity exceeds available quantity (<= $max_qty)."]);
        }

        $requisition = Requisition::firstOrCreate(
            [
                'project_order_id' => $project_order->id,
                'status' => Requisition::PENDING_STATUS,
                'requested_by' => auth()->id(),
            ],
            [
                'type' => Requisition::PROJECT_ORDER,
                'date' => Carbon::parse($validated['date_required']),
                'note' => 'Generated from Project Order ID ' . $project_order->id,
            ]
        );

        $requisitionItem = RequisitionItem::create([
            'product_id' => $requirement->storageItem->product?->id,
            'status' => Requisition::PENDING_STATUS,
            'requisition_id' => $requisition->id,
            'product_name' => $requirement->storageItem->product?->name,
            'requisition_qty' => $validated['quantity'],
            'pending_for_po_qty' => $validated['quantity'],
            'date' => Carbon::parse($validated['date_required']),
            'remark' => 'Generated from Project Order ID ' . $project_order->id,
            'isRelease' => false,
        ]);

        ProjectOrderRequirementUsed::create([
            'project_order_requirement_id' => $requirement->id,
            'project_order_id' => $project_order->id,
            'requisition_id' => $requisition->id,
            'requisition_item_id' => $requisitionItem->id,
            'quantity' => $requisitionItem['requisition_qty'],
        ]);

        $project_order->update(['quotation_generated_after_update' => false]);

        return redirect()->back()->with('success', 'Requirement used added/updated successfully.');
    }

    /**
     * Generate a quotation from the project order.
     *
     * @param ProjectOrder $project_order
     * @return RedirectResponse
     */
    public function generateQuotation(ProjectOrder $project_order): RedirectResponse
    {
        if ($project_order->project_quotation_id !== null) {
            $quotation = ProjectQuotation::find($project_order->project_quotation_id);
        } else {
            $quotation = ProjectQuotation::create([
                'project_order_id' => $project_order->id,
                'customer_id' => $project_order->customer_id,
                'vehicle_id' => $project_order->vehicle_id,
                'remark' => $project_order->remark,
                'sub_total' => 0,
                'discount' => 0,
                'gst_rate' => 0,
                'gst_total' => 0,
                'total' => 0,
                'status' => ProjectQuotation::STATUS_PENDING,
            ]);
            $quotation->update(['code' => ProjectQuotation::PREFIX . str_pad($quotation->id, 4, '0', STR_PAD_LEFT)]);
            $project_order->update(['project_quotation_id' => $quotation->id]);
        }

        foreach ($project_order->usedRequirements as $usedRequirement) {
            if ($usedRequirement->project_quotation_id && $usedRequirement->project_quotation_vehicle_part_id) {
                $usedRequirement->projectQuotationVehiclePart->update(['quantity' => $usedRequirement->quantity]);
            } else {
                $newPart = ProjectQuotationVehiclePart::create([
                    'project_quotation_id' => $quotation->id,
                    'storage_item_id' => $usedRequirement->requirement->storageItem->id,
                    'name' => $usedRequirement->requirement->storageItem->product->name,
                    'quantity' => $usedRequirement->quantity,
                ]);

                $usedRequirement->update([
                    'project_quotation_id' => $quotation->id,
                    'project_quotation_vehicle_part_id' => $newPart->id,
                ]);
            }
        }

        return back()->with('updated', 'Quotation created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProjectOrder $project_order
     * @param ProjectOrderRequirementUsed $requirementUsed
     * @return RedirectResponse
     */
    public function destroy(ProjectOrder $project_order, ProjectOrderRequirementUsed $requirementUsed): RedirectResponse
    {
        $requirementUsed->delete();

        return redirect("/project-orders/$project_order->id")->with('deleted', 'Project order requirement used deleted successfully.');
    }
}
