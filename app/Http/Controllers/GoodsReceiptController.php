<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptDocument;
use App\Models\GoodsReceiptItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\GIN;
use App\Models\RequisitionItem;
use App\Models\Requisition;
use App\Models\OrderedRequisitionItem;
use App\Models\Storage as StorageWarehouse;
use App\Models\StorageItem;
use App\Models\Store;
use App\Models\StoreProduct;
use App\Models\Location;
use App\Models\GoodsReceiptHistory;
use App\Models\ItemMovement;
use App\Models\OrderRequisition;
use App\Models\PurchaseGoodsReturn as GoodsReturn;
use Barryvdh\DomPDF\Facade\Pdf;
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
use Illuminate\Support\Facades\Validator;
use App\Services\GoodsReceiptService;
use App\Services\OrderService;
use App\Services\RequisitionService;
use App\Services\StorageService;
use Illuminate\Support\Facades\Mail;

class GoodsReceiptController extends Controller
{
    public $goodsReceiptService;
    public $orderService;
    public $requisitionService;
    public $storageService;

    public function __construct(
        GoodsReceiptService $goodsReceiptService,
        OrderService $orderService,
        RequisitionService $requisitionService,
        StorageService $storageService
    )
    {
        $this->goodsReceiptService = $goodsReceiptService;
        $this->orderService = $orderService;
        $this->requisitionService = $requisitionService;
        $this->storageService = $storageService;
        $this->authorizeResource(GoodsReceipt::class, 'goods_receipt');
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

        $statuses = GoodsReceipt::STATUS_TEXT_ARRAY;

        $gr_items = GoodsReceipt::with(['order' => function ($query) {
            $query->withTrashed();
        }, 'order.vendor', 'items.product'])
        ->when($search, function ($query) use ($search, $statuses) {
            $query->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('do_number', 'LIKE', "%{$search}%")
                  ->orWhere(function ($subQuery) use ($search, $statuses) {
                      foreach ($statuses as $statusId => $status) {
                          if (stripos($status, $search) !== false) {
                              $subQuery->orWhere('status', $statusId);
                          }
                      }
                  })
                  ->orWhereHas('order', function ($subQuery) use ($search) {
                      $subQuery->where('code', 'LIKE', "%{$search}%")
                               ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                                   $vendorQuery->where('name', 'LIKE', "%{$search}%")
                                               ->orWhere('code', 'LIKE', "%{$search}%");
                               });
                  });
        })
        // ->when(($order && $by), function ($query) use ($order, $by) {
        //     $query->orderBy($order, $by);
        // })
        ->orderBy('id', 'DESC')
        ->orderBy('status', 'ASC')
        ->paginate($paginate);


        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $gr_items->appends($queryString);

        return Inertia::render('GoodsReceipt/Index', [
            'gr_items' => $gr_items,
            'filters' => $filters,
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param GoodsReceipt $goods_receipt
     * @return Response
     */
    public function show(GoodsReceipt $goods_receipt): Response
    {
        $data = DB::table('goods_receipts')
            ->join('orders', 'goods_receipts.order_id', '=', 'orders.id')
            ->join('vendors', 'orders.vendor_id', '=', 'vendors.id')
            ->join('goods_receipt_items', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id') // Join goods_receipt with goods_receipt_item
            ->join('order_items', function($join) {
                $join->on('order_items.order_id', '=', 'orders.id') // Match order_id in order_item with orders
                     ->on('order_items.product_id', '=', 'goods_receipt_items.product_id'); // Match product_id in order_item with goods_receipt_item
            })
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('goods_receipts.id', $goods_receipt->id) // Add where condition
            ->select(
                'goods_receipt_items.id as goods_receipt_item_id',
                'goods_receipt_items.product_id',
                'goods_receipt_items.product_name',
                'categories.name as category',
                'products.sku as product_sku',
                'goods_receipt_items.quantity as balance_qty',
                'goods_receipt_items.receive_quantity',
                'order_items.id as order_item_id',
                'order_items.quantity as ordered_qty',
                'order_items.total as total',
                'orders.id as order_id'
            )
            ->get();
        $goods_receipt->load('order.vendor', 'documents');
        $goods_receipt->items = $data;
        return Inertia::render('GoodsReceipt/Detail', [
            'goodsReceipt' => $goods_receipt
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
            'order_id' => 'required|exists:orders,id',
            'requester_name' => 'required|array|min:1',
            'requester_name.*.name' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.receive_quantity' => 'required|integer|min:1',
        ], [
            'order_id.required' => 'The purchase order number is required.',
            'order_id.exists' => 'The selected purchase order does not exist.',
            'requester_name.required' => 'At least one requestor is required.',
            'requester_name.*.name.required' => 'The requestor name is required for all requestors.',
            'items.required' => 'At least one item is required.',
            'items.*.product_id.required' => 'The product is required for all items.',
            'items.*.product_id.exists' => 'The selected product for an item does not exist.',
            'items.*.quantity.required' => 'The quantity is required for all items.',
            'items.*.quantity.integer' => 'The quantity must be an integer for all items.',
            'items.*.quantity.min' => 'The quantity must be at least 1 for all items.',
            'items.*.receive_quantity.required' => 'The receive quantity is required for all items.',
            'items.*.receive_quantity.integer' => 'The receive quantity must be an integer for all items.',
            'items.*.receive_quantity.min' => 'The receive quantity must be at least 1 for all items.',
        ]);

        $data = $request->all();
        $data['requester_name'] = json_encode($data['requester_name']);
        $gr = GoodsReceipt::create($data);
        $gr->update(['code' => 'GRN' . str_pad($gr->id, 4, '0', STR_PAD_LEFT)]);
//        $gr->update(['qr_path' => self::generateRandomQrCode("/goods-receipts/$gr->id/pdf")]);

        /** Update product price **/
        foreach ($request->items as $item) {
            $item['goods_receipt_id'] = $gr->id;
            GoodsReceiptItem::create($item);
        }

        return redirect('/goods-receipts')->with('created', 'Goods Receipt created successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function updateReturned(Request $request): Redirector|RedirectResponse|Application
    {
        $this->authorize('update-goods-receipt');
        $id = $request->id;
        $gr_item = GoodsReceipt::find($id);

        if ($gr_item) {
            $gr_item->update(['is_return' => true]);
            return redirect('/goods-receipts')->with('updated', 'Goods receipt updated successfully');
        }

        return redirect('/goods-receipts')->with('updated', 'Goods receipt is not found!');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param GoodsReceipt $goodsReceipt
     * @param Request $request
     * @return RedirectResponse
     */
    public function uploadDocument(GoodsReceipt $goodsReceipt, Request $request): RedirectResponse
    {
        if ($request->type == '1') {
            $request->validate([
                'type' => 'required|in:1,0',
                'file_url' => $request->type == '1' ? 'required|mimes:pdf|max:10240' : '',
            ]);

            // Update PDF
            $path = $request->file('file_url')->store("receipt/{$goodsReceipt->id}", 'public');
            GoodsReceiptDocument::create([
                'goods_receipt_id' => $goodsReceipt->id,
                'file_name' => $request->file('file_url')->getClientOriginalName(),
                'file_url' => $path,
            ]);

            return back()->with('created', 'Upload document successfully');
        } else {
            $request->validate([
                'type' => 'required|in:1,0',
                'image_url' => $request->type == '0' ? 'required' : '',
                'image_name' => 'required|string|max:255',
            ]);

            // Update Image
            $base64Image = $request->input('image_url');
            $image = Image::make($base64Image);
            $filename = 'uploaded_image_' . time() . '.png';
            $image->save(storage_path("app/public/receipt/{$goodsReceipt->id}/{$filename}")); // Corrected storage path

            GoodsReceiptDocument::create([
                'goods_receipt_id' => $goodsReceipt->id,
                'file_name' => $request->image_name . ".png",
                'file_url' => "receipt/{$goodsReceipt->id}/{$filename}",
            ]);

            return back()->with('created', 'Upload image successfully');
        }
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param GoodsReceipt $goodsReceipt
     * @param GoodsReceiptDocument $document
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function deleteDocument(GoodsReceipt $goodsReceipt, GoodsReceiptDocument $document, Request $request): Redirector|RedirectResponse|Application
    {
        $this->authorize('delete-goods-receipt', GoodsReceipt::class);

        if (Storage::disk('public')->exists($document->file_url)) {
            Storage::disk('public')->delete($document->file_url);
        }

        $document->delete();

        return back()->with('deleted', 'Document deleted successfully');
    }


    /**
     * Display the specified resource.
     *
     * @param GoodsReceipt $goodsReceipt
     * @return \Illuminate\Http\Response
     */
    public function pdf(GoodsReceipt $goodsReceipt)
    {
        $data = [
            'title' => 'GOODS RECEIPT',
            'goodsReceipt' => $goodsReceipt->load([
                'order' => function ($query) {
                    $query->withTrashed();
                },
                'order.vendor',
                'items.product.category',
                'documents'
            ]),
        ];

        $pdf = Pdf::loadView('pdf.goods_receipt', $data);
        return $pdf->stream();
    }

    public function receiveItem(Request $request)
    {
        $request->validate(
            [
                'do_number' => 'required_if:type,receiving',
                'attachment' => 'required_if:type,receiving',
                'items.*.' . ($request->type === 'canceling' ? 'canceled_qty' : 'receiving_qty') => [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) use ($request) {
                        preg_match('/items\.(\d+)\.(receiving_qty|canceled_qty)/', $attribute, $matches);
                        $index = $matches[1] ?? null;
                        $field = $matches[2] ?? null;

                        if ($index !== null && isset($request->items[$index][$field])) {
                            $qty = $request->items[$index][$field];
                            $balance_qty = $request->items[$index]['balance_qty'];
                            if ($qty > $balance_qty) {
                                $fail(ucwords(str_replace('_', ' ', $field)) . " cannot exceed its balance quantity.");
                            }
                        }
                    },
                ],
            ],
            [
                'do_number.required_if' => 'DO Number is required.',
                'attachment.required_if' => 'Upload attachment.',
            ]
        );

        if ($request->type == 'receiving') {
            if (!collect($request->items)->pluck('receiving_qty')->filter(fn($qty) => $qty > 0)->count()) {
                return back()->withErrors(['items' => 'At least one receiving quantity must be greater than 0.'])->withInput();
            }
        } else {
            if (!collect($request->items)->pluck('canceled_qty')->filter(fn($qty) => $qty > 0)->count()) {
                return back()->withErrors(['items' => 'At least one canceled quantity must be greater than 0.'])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            if ($request->type == 'canceling') {
                // $this->goodsReceiptService->cancelBalance($request->all());

                $this->orderService->cancelBalance($request->all());
                $message = 'Goods receipt updated';
            } else {
                $goodsReceipt = GoodsReceipt::with('order')->find($request->goods_receipt_id);
                if ($request->hasFile('attachment')) {
                    $this->goodsReceiptService->uploadAttachment($request->attachment['file_url'], $goodsReceipt);
                }

                $goodsReceipt->update([
                    'status' => GoodsReceipt::STATUS_PARTIALLY_RECEIVED,
                    'do_number' => $request->do_number
                ]);

                $this->orderService->itemReceived($goodsReceipt, $request->items);
                $this->requisitionService->receivedOrderedRequisitionItem($goodsReceipt, $request->items);
                $this->storageService->createStorage($goodsReceipt, $request->items);
                $this->goodsReceiptService->updateGoodsReceiptItems($goodsReceipt, $request->items);
            }

            DB::commit();

            return redirect('/goods-receipts')->with('created', isset($message) ? $message : 'Items received successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return back()->with('deleted', $e->getMessage());
        }
    }

    public function requisitionTypeIsOther($requisition_id) {
        if (Requisition::find($requisition_id)->type == Requisition::OTHERS_TYPE) {
            return true;
        }
        return false;
    }

    public function setVoid(GoodsReceipt $goodsReceipt) {
        $goodsReceipt->update(['status' => GoodsReceipt::STATUS_VOID]);
        return redirect('/goods-receipts')->with('created', 'Goods receipt has been voided');
    }

    // public function confirm(GoodsReceipt $goodsReceipt) {
    //     // $goodsReceipt->update(['status' => GoodsReceipt::STATUS_CONFIRMED]);
    //     $goodsReceipt = GoodsReceipt::where('order_id', $goodsReceipt->order_id)->where('status', GoodsReceipt::STATUS_COMPLETED)->get();
    //     dd($goodsReceipt);
    //     return redirect('/goods-receipts')->with('created', 'Goods receipt has been confirmed');
    // }

    public function convertToGoodsReturn(GoodsReceipt $goodsReceipt) {
        $goodsReceipt->update(['is_return' => 1]);
        $goodsReturn = GoodsReturn::create([
            'goods_receipt_id' => $goodsReceipt->id,
            'created_by' => auth()->user()->id,
            'status' => GoodsReturn::STATUS_PENDING
        ]);
        return redirect('/purchase-goods-return/' . $goodsReturn->id)->with('created', 'Goods return has been created');
    }

    public function redirectToGoodsReturn(GoodsReceipt $goodsReceipt) {
        dd('test');
        $goodsReturn = GoodsReturn::where('goods_receipt_id', $goodsReceipt->id)->where('status', GoodsReturn::STATUS_PENDING)->first();
        return redirect('/purchase-goods-return/' . $goodsReturn->id);
    }

}
