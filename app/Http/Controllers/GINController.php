<?php

namespace App\Http\Controllers;

use App\Models\GIN;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptDocument;
use App\Models\GoodsReceiptItem;
use App\Models\ItemMovement;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReturnGIN;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Store;
use App\Models\Location;
use App\Models\StoreProduct;
use App\Models\User;
use App\Models\GINItem;
use Couchbase\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;
use DB;

class GINController extends Controller
{
    public function __construct()
    {
//        $this->authorizeResource(GIN::class, 'gin');
    }

    /**
     * Display a listing of the resource.
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

        $gins = GIN::with('requisition.createdBy', 'createdBy', 'issuedTo')
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $gins->appends($queryString);

        return Inertia::render('GIN/Index', [
            'gins' => $gins,
            'filters' => $filters,
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param GIN $goods_issue_note
     * @return Response
     */
    public function show(GIN $goods_issue_note)
    {
        $goods_issue_note->load('items.requisitionItem.product.uom', 'returns.ginItem.requisitionItem.product.uom');
        $users = self::toSelect2Data(User::all()->toArray(), 'id', 'username');
        return Inertia::render('GIN/Detail', [
            'gin' => $goods_issue_note,
            'users' => $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param GIN $gin
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function updateRemark(GIN $gin, Request $request): Redirector|RedirectResponse|Application
    {
        $this->authorize('update-gin');
        $gin->update(['remark' => $request->remark]);
        return back()->with('created', 'GIN updated successfully');
    }


    // public function return(GIN $gin)
    // {
    //     return Inertia::render('GIN/Form', [
    //         'gin' => $gin->load('requisition', 'requisitionItem.product.uom'),
    //     ]);
    // }

    public function returnUpdate(GIN $gin, Request $request)
    {
        $request->validate([
            'gin_item_id' => 'required|exists:gin_items,id',
            'return_qty' => 'required|numeric|min:1',
            'remark' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $ginItem = GINItem::findOrFail($request->input('gin_item_id'));
            $return_qty = $request->input('return_qty');

            if ($return_qty > $ginItem->issued_qty) {
                return back()->withErrors([
                    'return_qty' => 'Return quantity cannot exceed issued quantity.'
                ]);
            }

            $requisitionItem = $ginItem->requisitionItem;
            $product = $requisitionItem->product;

            // Update GIN Item or delete if all quantity is returned
            if ($return_qty == $ginItem->issued_qty) {
                $ginItem->delete();
            } else {
                $ginItem->returned_qty += $return_qty;
                $ginItem->issued_qty -= $return_qty;
                $ginItem->save();
            }

            // Update Requisition Item or delete if all quantity is returned
            if ($return_qty == $requisitionItem->requested_qty) {
                $requisitionItem->delete();
            } else {
                $requisitionItem->issued_qty -= $return_qty;
                $requisitionItem->pending_issue_qty += $return_qty;
                $requisitionItem->save();
            }

            // Update Product stock
            $product->update([
                'stock' => ($product->stock ?? 0) + $return_qty,
            ]);

            // Log return
            ReturnGIN::create([
                'gni_id' => $ginItem->gin_id,
                'gni_item_id' => $ginItem->id,
                'return_qty' => $return_qty,
                'serial' => $gin->code,
                'reason' => $request->input('remark'),
            ]);

            DB::commit();

            // Return a success response with Inertia
            return redirect()->route('goods-issue-notes.show', $gin->id)->with('created', 'GIN updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Return Update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['error' => 'Failed to return item to inventory. Please try again.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param GIN $goods_issue_note
     * @return RedirectResponse
     */
    public function destroy(GIN $goods_issue_note): RedirectResponse
    {
        $goods_issue_note->delete();

        return back()->with('deleted', 'Goods issue note return successfully');
    }

    public function confirm(GIN $gin, Request $request)
    {
        $request->validate([
            'items.*.issuing_qty' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    preg_match('/items\.(\d+)\.issuing_qty/', $attribute, $matches);
                    $index = $matches[1] ?? null;

                    if ($index !== null && isset($request->items[$index]['issuing_qty'])) {
                        if ($value > $request->items[$index]['order_qty']) {
                            $fail("Issuing quantity cannot exceed its requested quantity");
                        }
                    }
                },
            ],
        ], [
            'items.*.issuing_qty.required' => 'Issuing qty is required'
        ]);

        $gin->update([
            'status' => GIN::CONFIRMED_STATUS,
            'issued_to' => $request->issued_to,
        ]);

        foreach ($request->items as $item) {
            $ginItem = GINItem::find($item['id']);
            $ginItem->update([
                'order_qty' => $item['order_qty'],
                'issued_qty' => $item['issuing_qty']
            ]);
            $requisitionItem = RequisitionItem::find($ginItem->requisition_item_id);
            $requisitionItem->update([
                'pending_issue_qty' => 0,
                'committed_qty' => $requisitionItem->committed_qty - $item['issuing_qty'],
                'issued_qty' => $requisitionItem->issued_qty + $item['issuing_qty'],
            ]);
            if ($requisitionItem->issued_qty == $requisitionItem->requested_qty) {
                $requisitionItem->update(['status' => RequisitionItem::APPROVED_STATUS]);
            }

            $product = Product::find($requisitionItem->product_id);
            if ($product) {
                $product->subStock($item['issuing_qty'])
                    ->subCommittedQty($item['issuing_qty'])
                    ->subRequestedQty($item['issuing_qty']);
            }

            if ($requisitionItem->issued_qty == $requisitionItem->requested_qty) {
                $requisitionItem->update(['status' => RequisitionItem::COMPLETED_STATUS]);
            }

            $allItemsCompleted = RequisitionItem::where('requisition_id', $requisitionItem->requisition_id)
                ->where('status', '!=', RequisitionItem::COMPLETED_STATUS)
                ->doesntExist();

            if ($allItemsCompleted) {
                Requisition::find($requisitionItem->requisition_id)->update(['status' => Requisition::COMPLETED_STATUS]);
            }

            // Create an ItemMovement entry
//            ItemMovement::create([
//                'product_id' => $product->id,
//                'store_id' => $item['store_id'] ?? null,
//                'movement_type' => ItemMovement::MOVEMENT_TYPE_MINUS,
//                'transaction_type' => ItemMovement::GIN_TYPE,
//                'transaction_id' => $gin->id,
//                'quantity' => $item['issuing_qty'],
//                'remark' => "Goods issued for GIN #{$gin->code}",
//            ]);
        }
        return redirect('/goods-issue-notes')->with('created', 'Goods issue note confirmed');
    }
}
