<?php

namespace App\Http\Controllers;

use App\Models\GIN;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Vendor;
use App\Models\GINItem;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use DB;

class RequisitionToGINController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $search = $request->search;
        $transaction_type = $request->transaction_type;
        $requisitionable_id = $request->requisitionable_id;


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
        $filters['transaction_type'] = $transaction_type;
        $filters['requisitionable_id'] = $requisitionable_id;

        $typeNames = Requisition::ORDER_TYPE_ARRAY;

        $requisition_items = RequisitionItem::with([
            'requisition.requisitionable',
            'requisition.order',
            'requisition.createdBy',
            'product.uom'
        ])
        ->where('status', RequisitionItem::PENDING_STATUS)
        ->when($search, function ($query) use ($search, $typeNames) {
            $query->where('product_name', 'LIKE', "%{$search}%");
            $query->orWhere('uom', 'LIKE', "%{$search}%");
            $query->orWhereHas('requisition', function ($subQuery) use ($search, $typeNames) {
                $subQuery->where('code', 'LIKE', "%{$search}%");
                $subQuery->orWhere(function ($subQuery) use ($search, $typeNames) {
                    foreach ($typeNames as $typeId => $typeName) {
                        if (stripos($typeName, $search) !== false) {
                            $subQuery->orWhere('type', $typeId);
                        }
                    }
                });
            });
            $query->orWhereHas('product', function ($subQuery) use ($search) {
                $subQuery->where('sku', 'LIKE', "%{$search}%");
            });
            $query->orWhereHas('requisition.createdBy', function ($subQuery) use ($search) {
                $subQuery->where('name', 'LIKE', "%{$search}%");
            });
        })
        ->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })
        ->whereHas('requisition', function ($query) use ($transaction_type, $requisitionable_id) {
            $query->where('status', Requisition::APPROVED_STATUS)
                  ->when($transaction_type !== null, function ($query) use ($transaction_type) {
                      $query->where('type', $transaction_type);
                  })
                  ->when($requisitionable_id !== null, function ($query) use ($requisitionable_id) {
                      $query->where('requisitionable_id', $requisitionable_id);
                  });
        })
        ->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $requisition_items->appends($queryString);
        $transaction_types = Requisition::ORDER_TYPE_ARRAY;

        return Inertia::render('RequisitionToGIN/Index', [
            'csrf' => csrf_token(),
            'requisition_items' => $requisition_items,
            'filters' => $filters,
            'transaction_types' => $transaction_types,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $requisition_items = [];

        if ($request->has('items') && is_array($request->items)) {
            $requisition_items = RequisitionItem::whereIn('id', $request->items)->with('requisition.order.vendor', 'product.uom')->get()->toArray();
        }

        $vendors = self::toSelect2Data(Vendor::all()->toArray(), 'id', 'code', 'name', ' | ');
        return Inertia::render('RequisitionToGIN/Form', [
            'requisition_items' => $requisition_items,
            'vendors' => $vendors,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    public function store(Request $request): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.requisition_id' => 'required|exists:requisitions,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ], [
            'items.required' => 'The items field is required.',
            'items.array' => 'The items must be an array.',
            'items.*.requisition_id.required' => 'The requisition_id field is required for each item.',
            'items.*.requisition_id.exists' => 'The selected requisition_id is invalid for one or more items.',
            'items.*.quantity.required' => 'The quantity field is required for each item.',
            'items.*.quantity.numeric' => 'The quantity must be a number for each item.',
            'items.*.quantity.min' => 'The quantity must be at least 1 for each item.',
        ]);

        foreach ($request->items as $item) {
            $item['status'] = GIN::PENDING_STATUS;
            $gin = GIN::create($item);
            $gin->update(['code' => 'GIN' . str_pad($gin->id, 4, '0', STR_PAD_LEFT)]);
        }

        return redirect('/goods-issue-notes')->with('created', 'Goods Issue Note created successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    public function convert(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validate that 'items' is present and is an array
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*' => 'exists:requisition_items,id',
            ]);

            // Initialize an array to hold already converted items
            $alreadyConvertedItems = [];

            // Fetch all RequisitionItems in one query
            $requisitionItems = RequisitionItem::with(['requisition.requisitionable', 'ginItems'])
                ->whereIn('id', $request->items)
                ->get();

            // Check each item to see if it has already been converted
            foreach ($requisitionItems as $requisitionItem) {
                if ($requisitionItem->ginItems()->exists()) {
                    $alreadyConvertedItems[] = $requisitionItem->id; // or use a unique identifier like name
                }
            }

            // If there are already converted items, abort and return error
            if (!empty($alreadyConvertedItems)) {
                DB::rollBack();
                return redirect()->back()->withErrors([
                    'converted' => 'The following items have already been converted: ' . implode(', ', $alreadyConvertedItems)
                ]);
            }

            // Proceed with conversion since no items are pre-converted
            $requisitionItem = $requisitionItems->first();
            $transaction_type = $requisitionItem->requisition->type_text;
            $transaction = $requisitionItem->requisition->requisitionable;
            $transaction_code = $transaction ? $transaction->code : null;

            $gin = GIN::create([
                'status' => GIN::PENDING_STATUS,
                'created_by' => auth()->user()->id,
                'transaction_type' => $transaction_type,
                'transaction_code' => $transaction_code
            ]);
            $gin->update(['code' => 'GIN' . str_pad($gin->id, 4, '0', STR_PAD_LEFT)]);

            foreach ($requisitionItems as $requisitionItem) {
                $requisitionItem->update([
                    'pending_issue_qty' => $requisitionItem->committed_qty,
                ]);

                GINItem::create([
                    'gin_id' => $gin->id,
                    'requisition_id' => $requisitionItem->requisition_id,
                    'requisition_item_id' => $requisitionItem->id,
                ]);
            }

            DB::commit();
            return redirect(url('goods-issue-notes/' . $gin->id))->with('created', 'Converted to Goods Issue Note');
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getTraceAsString();
        }
    }


    /**
     * Update cancel status for requisition item.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    public function cancel(Request $request)
    {
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $item_id) {
                $requisition_item = RequisitionItem::find($item_id);

                if ($requisition_item) {
                    $requisition_item->update(['status' => RequisitionItem::REJECTED_STATUS]);
                }
            }
        }

        return redirect('/requisitions-to-order')->with('deleted', 'Requisition canceled successfully');
    }
}
