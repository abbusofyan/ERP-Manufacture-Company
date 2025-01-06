<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\ServiceOrder;
use App\Models\Vendor;
use App\Models\GIN;
use App\Models\Requisitionable;
use App\Models\ItemMovement;
use App\Models\Store;
use App\Models\User;
use App\Models\RequisitionRejectNote;
use App\Models\Category;
use Spatie\Permission\Models\Role;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;
use DB;
use App\Services\RequisitionService;
use App\Http\Requests\CreateRequisitionRequest;
use App\Http\Requests\UpdateRequisitionRequest;
use App\Http\Requests\SendRequisitionForApprovalRequest;

class RequisitionController extends Controller
{

    protected $requisitionService;

    public function __construct(RequisitionService $requisitionService)
    {
        // $this->authorizeResource(Requisition::class, 'requisition');
        $this->requisitionService = $requisitionService;

    }

    public function index(Request $request): Response
    {
        $filters = [
            'search' => $request->input('search', ''),
            'order' => $request->input('order', 'id'),
            'by' => $request->input('by', 'desc'),
            'paginate' => $request->input('paginate', 10),
        ];

        $requisition = $this->requisitionService->getRequisitionDatatable($filters);

        return Inertia::render('Requisition/Index', [
            'requisitions' => $requisition,
            'filters' => $filters,
            'managers' => toSelect2Data(User::role('manager')->get()->toArray(), 'id', 'name'),
            'csrf' => csrf_token()
        ]);
    }

    public function show(Requisition $requisition): Response
    {
        $requisition->load(
            'createdBy',
            'approvedBy',
            'requisitionItems.product.uom',
            'requisitionItems.product.category',
            'requisitionItems.product.photos',
            'orders.vendor',
            'rejectionNotes'
        );
        return Inertia::render('Requisition/Detail', [
            'requisition' => $requisition,
        ]);
    }


    public function create(Request $request): Response
    {
        $defaultStoreId = Store::where('name', Store::DEFAULT_STORE)->first()->id;
        return Inertia::render('Requisition/Form', [
            'csrf' => csrf_token(),
            'types' => Requisition::ORDER_TYPE_ARRAY_SELECT,
            'warehouses' => toSelect2Data(Store::all()->toArray(), 'id', 'name'),
            'default_warehouse_id' => $defaultStoreId
        ]);
    }

    public function store(CreateRequisitionRequest $request)
    {
        try {
            $requisition = $this->requisitionService->createRequisition($request->all());
            if ($request->status == Requisition::APPROVED_STATUS) {
                return redirect("/requisitions/{$requisition->id}/approve");
            }
            return redirect('/requisitions')->with('created', 'Requisition created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('deleted', 'Failed to create requisition: ' . $e->getMessage());
        }
    }


    public function edit(Requisition $requisition): Response
    {
        $requisition->load('requisitionItems.product.uom', 'requisitionItems.product.photos', 'requisitionItems.product.category');
        foreach ($requisition->requisitionItems as $key => $requisitionItem) {
            $requisition->requisitionItems[$key]->select2Warehouse = toSelect2Data(Store::where('id', $requisitionItem->store_id)->get()->toArray(), 'id', 'name');
            $requisition->requisitionItems[$key]->product->select2Category = toSelect2Data(Category::where('id', $requisitionItem->product->category_id)->get()->toArray(), 'id', 'name');
            $requisition->requisitionItems[$key]->product->select2ItemID = toSelect2Data(Product::where('id', $requisitionItem->product_id)->get()->toArray(), 'id', 'sku', 'name', ' | ');
        }

        /** Get all requisition item product **/

        return Inertia::render('Requisition/Form', [
            'csrf' => csrf_token(),
            'types' => Requisition::ORDER_TYPE_ARRAY_SELECT,
            'orders' => toSelect2Data(Order::where('id', $requisition->order_id)->get()->toArray(), 'id', 'code'),
            'serviceOrders' => toSelect2Data(ServiceOrder::where('id', $requisition->service_order_id)->get()->toArray(), 'id', 'code'),
            'requisition' => $requisition,
            'warehouses' => self::toSelect2Data(Store::all()->toArray(), 'id', 'name'),
            'categories' => self::toSelect2Data(Category::all()->toArray(), 'id', 'name', 'prefix', ' | '),
        ]);
    }


    public function update(UpdateRequisitionRequest $request, Requisition $requisition)
    {
        try {
            $this->requisitionService->updateRequisition($requisition->id, $request->all());
            if (auth()->user()->hasPermissionTo('approve-requisition')) {
                return redirect("/requisitions/{$requisition->id}/approve");
            }
            return redirect('/requisitions')->with('created', 'Requisition updated successfully');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with('deleted', 'Failed to update requisition');
        }
    }



    public function release(Request $request)
    {

        $data = $request->form;

        DB::beginTransaction();

        try {
            foreach ($data['items'] as $requisition_item_id) {
                $requisitionItem = RequisitionItem::find($requisition_item_id);

                $product = Product::find($requisitionItem->product_id);
                if ($product) {
                    $product->subStock($requisitionItem->committed_qty)
                    ->subCommittedQty($requisitionItem->committed_qty)
                    ->subRequestedQty($requisitionItem->committed_qty);
                }

                $requisitionItem->update([
                    'issued_qty' => $requisitionItem->issued_qty + $requisitionItem->committed_qty,
                    'committed_qty' => 0,
                ]);

                if ($requisitionItem->issued_qty == $requisitionItem->requested_qty) {
                    $requisitionItem->update(['status' => RequisitionItem::APPROVED_STATUS]);
                }

                $gin = GIN::create([
                    'requisition_id' => $requisitionItem->requisition_id,
                    'requisition_item_id' => $requisitionItem->id,
                    'quantity' => $requisitionItem->issued_qty,
                    'status' => GIN::CONFIRMED_STATUS,
                    'release_to' => $data['release_to'],
                    'created_by' => auth()->user()->id
                ]);
                $gin->update(['code' => 'GIN' . str_pad($gin->id, 4, '0', STR_PAD_LEFT)]);

                $allItemsCompleted = RequisitionItem::where('requisition_id', $requisitionItem->requisition_id)
                                     ->where('status', '!=', RequisitionItem::APPROVED_STATUS)
                                     ->doesntExist();

                if ($allItemsCompleted) {
                    Requisition::find($requisitionItem->requisition_id)->update(['status' => Requisition::APPROVED_STATUS]);
                }
                if ($product) {
                    ItemMovement::create([
                        'product_id' => $requisitionItem->product_id,
                        'store_id' => Store::where('name', Store::DEFAULT_STORE)->first()->id,
                        'movement_type' => ItemMovement::MOVEMENT_TYPE_MINUS,
                        'transaction_type' => ItemMovement::REQUISITION_TYPE,
                        'transaction_id' => $requisitionItem->requisition_id,
                        'quantity' => $requisitionItem->issued_qty
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('created', 'Item released');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return redirect()->back()->with('deleted', 'Failed to release item: ' . $e->getMessage());
        }
    }



    public function destroy(Requisition $requisition): RedirectResponse
    {
        $requisition->delete();

        return redirect('/requisitions')->with('deleted', 'Requisition deleted successfully');
    }


    public function sendForApproval(SendRequisitionForApprovalRequest $request, Requisition $requisition)
    {
        try {
            DB::beginTransaction();
            $this->requisitionService->sendForApproval($requisition, $request->user_id);
            DB::commit();
            return redirect('/requisitions')->with('updated', 'Requisition has been sent for approval');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('deleted', 'An error occurred while sending the requisition for approval');
        }
    }

    public function approve(Request $request, Requisition $requisition) {
        try {
            $this->requisitionService->approveRequisition($requisition, $request);
            return redirect('/requisitions')->with('created', 'Requisition approved');
        } catch (\Exception $e) {
            return redirect()->back()->with('deleted', 'Failed to approve requisition');
        }
    }

    public function reject(Request $request, Requisition $requisition) {
        $requisition->update([
            'status' => Requisition::REJECTED_STATUS
        ]);
        $note = RequisitionRejectNote::create([
            'requisition_id' => $requisition->id,
            'note' => $request->reason ?? '-'
        ]);
        return redirect('/requisitions')->with('created', 'Requisition rejected');
    }

    public function void(Request $request, Requisition $requisition) {
        $requisition->load('requisitionItems');
        try {
            $requisition->update(['status' => Requisition::VOID_STATUS]);
            return redirect('/requisitions')->with('updated', 'Requisition voided');
        } catch (\Exception $e) {
            return redirect()->back()->with('deleted', 'Failed to void requisition');
        }
    }


    public function cancelItem(Requisition $requisition, RequisitionItem $item, Request $request)
    {
        // Ensure the item belongs to the requisition
        if ($item->requisition_id !== $requisition->id) {
            return redirect()->back()->with('error', 'Item does not belong to this requisition.');
        }

        // Check if the item can be cancelled (e.g., status is 'Pending')
        if ($item->status !== RequisitionItem::PENDING_STATUS) {
            return redirect()->back()->with('error', 'Only pending items can be cancelled.');
        }

        DB::beginTransaction();

        try {
            // Restore the stock
            $product = $item->product;
            if ($product) {
                $product->committed_qty -= $item->requested_qty;
                $product->save();
            }

            // Delete the requisition item
            $item->delete();

            DB::commit();

            return redirect()->back()->with('created', 'Item has been cancelled and stock restored successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to cancel item.');
        }
    }

    public function itemChecked(Request $request) {
        RequisitionItem::find($request->requisition_item_id)->update([
            'checked_by' => auth()->user()->id,
            'notes' => $request->comment
        ]);
        return redirect('/requisitions-to-order')->with('created', 'Item checked.');
    }

    public function editRemark(RequisitionItem $requisitionItem, Request $request) {
        $requisitionItem->remark = $request->remark;
        $requisitionItem->save();
        return redirect()->back()->with('created', 'Remark updated.');
    }

    public function duplicate(Requisition $requisition) {
        $requisition->load('requisitionItems.product.uom', 'requisitionItems.product.photos', 'requisitionItems.product.category');
        foreach ($requisition->requisitionItems as $key => $requisitionItem) {
            $requisition->requisitionItems[$key]->select2Warehouse = toSelect2Data(Store::where('id', $requisitionItem->store_id)->get()->toArray(), 'id', 'name');
            $requisition->requisitionItems[$key]->product->select2Category = toSelect2Data(Category::where('id', $requisitionItem->product->category_id)->get()->toArray(), 'id', 'name');
            $requisition->requisitionItems[$key]->product->select2ItemID = toSelect2Data(Product::where('id', $requisitionItem->product_id)->get()->toArray(), 'id', 'sku', 'name', ' | ');
        }
        return Inertia::render('Requisition/Form', [
            'requisition' => $requisition,
            'types' => Requisition::ORDER_TYPE_ARRAY_SELECT,
            'warehouses' => toSelect2Data(Store::all()->toArray(), 'id', 'name'),
            'csrf' => csrf_token()
        ]);
    }

    public function cancelPendingOrder(RequisitionItem $requisitionItem): RedirectResponse
    {
        $requisitionService = new RequisitionService;
        $requisitionService->cancelPendingOrder($requisitionItem);
        return redirect()->back()->with('created', 'Requisition item canceled');
    }
}
