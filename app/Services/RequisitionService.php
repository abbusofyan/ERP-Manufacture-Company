<?php

namespace App\Services;

use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Product;
use App\Models\UnitOfMeasurement;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderRequisition;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\GoodsReceipt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use App\Mail\RequisitionApprovalMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ProductController;
use App\Mail\GoodsReceivedNotificationMail;
use App\Services\ProductService;

class RequisitionService
{

    public $productService;

    public function __construct(ProductService $productService) {
        $this->productService = $productService;
    }

    public function createRequisition(array $data) {
        DB::beginTransaction();

        try {
            $requisition = Requisition::create([
                'status' => $data['status'],
                'type' => $data['type'],
                'department' => $data['department'],
                'is_urgent' => $data['urgent_orders'],
                'note' => isset($data['note']) ? $data['note'] : null,
                'created_by' => auth()->user()->id,
                'required_date' => $data['date'] ? date('Y-m-d', strtotime($data['date'])) : null,
                'requisitionable_id' => $data['model_id'],
                'requisitionable_type' => Requisition::ORDER_TYPE_MODEL_ARRAY[$data['type']],
            ]);
            if (!empty($data['inventory_items'])) {
                foreach ($data['inventory_items'] as $item) {
                    if (isset($item['new_item']) && $item['new_item']) {
                        $productPayload = [
                            'type' => 'Non-Inventory',
                            // 'sku' => $item['product_id'],
                            'sku' => Product::generateItemID(),
                            'name' => $item['product_name'],
                            'uom_id' => UnitOfMeasurement::where('code', $item['uom_code'])->first()->id,
                            'category_id' => $item['category_id'],
                            'store_id' => $item['store_id'],
                            'created_by' => auth()->user()->id,
                            'auto_reorder' => 0,
                            'stock' => 0,
                            'status' => Product::STATUS_ACTIVE
                        ];

                        $inventoryService = new InventoryService;

                        $product = $inventoryService->createNewProduct($productPayload);

                        if (isset($item['photos'])) {
                            $inventoryService->uploadProductPhoto($item['photos'], $product);
                        }
                    } else {
                        $product = Product::find($item['product_id']);
                    }

                    $itemPayload = [
                        'requisition_id' => $requisition->id,
                        'product_id' => $product->id,
                        'product_name' => $item['product_name'],
                        'uom' => $item['uom_code'],
                        'store_id' => $item['store_id'],
                        'requested_qty' => $item['requested_qty'],
                        'remark' => $item['remark'],
                        'required_date' => $item['date'] ? date('Y-m-d', strtotime($item['date'])) : null,
                    ];
                    $itemPayload['status'] = RequisitionItem::PENDING_STATUS;

                    RequisitionItem::create($itemPayload);

                }
            }

            // if ($data['status'] == Requisition::PENDING_STATUS) {
            //     $this->sendForApproval($requisition, $data['user_id']);
            // }

            DB::commit();
            return $requisition;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to create requisition: ' . $e->getMessage());
        }
    }

    public function sendForApproval(Requisition $requisition, Array $user_id) {
        DB::beginTransaction();
        try {
            $selectedManager = json_decode($requisition->send_approval_to, true);
            if ($selectedManager) {
                $sendApprovalTo = array_unique(array_merge($selectedManager, $user_id));
            } else {
                $sendApprovalTo = $user_id;
            }

            $requisition->update([
                'status' => Requisition::PENDING_STATUS,
                'send_approval_to' => json_encode($sendApprovalTo)
            ]);

            $users = User::find($user_id);
            foreach ($users as $user) {
                Mail::to($user->email)->queue(new RequisitionApprovalMail($user, $requisition));
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to create requisition: ' . $e->getMessage());
        }

    }

    public function updateRequisition($requisition_id, array $data) {
        DB::beginTransaction();

        try {
            $requisition = Requisition::find($requisition_id);
            $requisition->update([
                'status' => $data['status'],
                'type' => $data['type'],
                'is_urgent' => $data['urgent_orders'],
                'note' => isset($data['note']) ? $data['note'] : null,
                'required_date' => $data['date'] ? date('Y-m-d', strtotime($data['date'])) : null,
                'requisitionable_id' => $data['model_id'],
                'requisitionable_type' => Requisition::ORDER_TYPE_MODEL_ARRAY[$data['type']],
            ]);

            RequisitionItem::where('requisition_id', $requisition_id)->forceDelete();
            if (!empty($data['inventory_items'])) {
                foreach ($data['inventory_items'] as $item) {
                    $itemPayload = [
                        'requisition_id' => $requisition_id,
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'uom' => $item['uom_code'],
                        'requested_qty' => $item['requested_qty'],
                        'remark' => $item['remark'],
                        'required_date' => $item['date'] ? date('Y-m-d', strtotime($item['date'])) : null,
                    ];
                    $itemPayload['status'] = RequisitionItem::PENDING_STATUS;
                    RequisitionItem::create($itemPayload);
                }
            }
            DB::commit();
            return $requisition;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function approveRequisition(Requisition $requisition, $request) {
        DB::beginTransaction();

        try {
            $requisition->update(['status' => Requisition::APPROVED_STATUS, 'approved_by' => auth()->user()->id]);

            $requisition->load('requisitionItems');

            if ($requisition->type == Requisition::OTHERS_TYPE) {
                $this->approveOtherTypeRequisition($requisition, $request);
            } else {
                foreach ($requisition->requisitionItems as $item) {
                    $product = Product::find($item->product_id);

                    $stock = $product ? $product->available_stock : 0;
                    if ($stock <= 0) {
                        $itemPayload['pending_order_qty'] = $item->requested_qty;
                    } elseif ($stock < $item['requested_qty']) {
                        $itemPayload['pending_order_qty'] = $item->requested_qty - $stock;
                        $itemPayload['committed_qty'] = $stock;
                    } else {
                        $itemPayload['committed_qty'] = $item->requested_qty;
                    }
                    $itemPayload['requested_by'] = $requisition->created_by;

                    $item->update($itemPayload);

                    $qtyToCommit = 0;
                    if ($stock > 0) {
                        if ($stock < $item['requested_qty']) {
                            $qtyToCommit = $stock;
                        } elseif ($stock >= $item['requested_qty']) {
                            $qtyToCommit = $item['requested_qty'];
                        }
                    }
                }
            }

            $this->productService->updateRequestedQty($requisition);

            DB::commit();

            return $requisition;
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            throw new \Exception('Failed to approve requisition');
        }
    }

    public function approveOtherTypeRequisition(Requisition $requisition, $request) {
        $requisition->load('requisitionItems');
        foreach ($requisition->requisitionItems as $item) {
            $itemPayload['pending_order_qty'] = $item['requested_qty'];
            $itemPayload['requested_by'] = $requisition->created_by;
            $requisitionItem = requisitionItem::find($item['id']);
            $requisitionItem->update($itemPayload);
        }
        return $requisition;
    }

    public function createProductionOrderRequisition(Collection $productionOrder) {

    }

    public function updateRequisitionItemOrderQuantity(Order $order) {
        $order->load('items');
        foreach ($order->items as $item) {
            $orderRequisitions = OrderRequisition::where('order_id', $item->order_id)->where('order_item_id', $item->id)->get();
            foreach ($orderRequisitions as $key => $orderRequisition) {
                $reqItem = RequisitionItem::find($orderRequisition->requisition_item_id);
                $reqItem->update([
                    'status' => RequisitionItem::PENDING_STATUS,
                    'ordered_qty' => $reqItem->pending_order_qty,
                    'pending_order_qty' => 0,
                ]);
                $requisition = Requisition::with('createdBy')->find($orderRequisition->requisition_id);
            }
        }
    }

    public function setCompleted(Collection $orderRequisitions) {
        foreach ($orderRequisitions as $key => $orderRequisition) {
            $orderItem = OrderItem::find($orderRequisition->order_item_id);
            $reqItem = RequisitionItem::find($orderRequisition->requisition_item_id);
            $reqItem->ordered_qty = $orderItem->quantity - $orderItem->canceled_qty;
            if ($reqItem->ordered_qty == 0) {
                $reqItem->status = RequisitionItem::COMPLETED_STATUS;
            }
            $reqItem->save();

            $allItemsCompleted = RequisitionItem::where('requisition_id', $orderRequisition->requisition_id)
                ->where('status', '!=', RequisitionItem::COMPLETED_STATUS)
                ->doesntExist();

            if ($allItemsCompleted) {
                Requisition::find($orderRequisition->requisition_id)->update(['status' => Requisition::COMPLETED_STATUS]);
            }
        }
    }

    public function cancelPendingOrder(RequisitionItem $requisitionItem) {
        DB::beginTransaction();
        try {
            $requisitionItemOriginal =  clone $requisitionItem;

            $requisitionItem->decrement('requested_qty', $requisitionItem->pending_order_qty);
            $requisitionItem->pending_order_qty = 0;
            $requisitionItem->save();

            $this->productService->cancelItemPendingOrder($requisitionItemOriginal);

            DB::commit();
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
        }
    }

    public function receivedOrderedRequisitionItem(GoodsReceipt $goodsReceipt, $data) {
        foreach ($data as $item) {
            $requisition = OrderRequisition::with('requisition')
                ->where('order_id', $item['order_id'])
                ->where('order_item_id', $item['order_item_id'])
                ->first()->requisition;

            if ($requisition->type != Requisition::OTHERS_TYPE) {
                $requisitionItems = RequisitionItem::with('requisition.approvedBy', 'requisition.createdBy', 'product')
                    ->where('product_id', $item['product_id'])
                    ->where(function ($query) {
                        $query->where('ordered_qty', '>', 0)
                            ->orWhere('pending_order_qty', '>', 0);
                    })
                    ->whereHas('requisition', function($query) {
                        $query->where('type', '!=', Requisition::OTHERS_TYPE);
                    })->get();

                $receivingQty = $item['receiving_qty'];

                foreach ($requisitionItems as $requisitionItem) {
                    if ($receivingQty > 0) {
                        $qtyToCommit = min($receivingQty, $requisitionItem->ordered_qty ?: $requisitionItem->pending_order_qty);
                        $requisitionItem->update([
                            'committed_qty' => $requisitionItem->committed_qty + $qtyToCommit,
                            'ordered_qty' => max(0, $requisitionItem->ordered_qty - $qtyToCommit),
                            'pending_order_qty' => max(0, $requisitionItem->pending_order_qty - $qtyToCommit),
                        ]);

                        $requisitionItem->product->addCommittedQty($qtyToCommit);

                        $receivingQty -= $qtyToCommit;

                        if ($requisitionItem->requisition->is_urgent) {
                            $HOD = $requisitionItem->requisition->approvedBy;
                            $requestor = $requisitionItem->requisition->createdBy;

                            $emailHOD = $HOD->email;
                            $emailRequestor = $requestor->email;
                            Mail::to($emailHOD)->queue(new GoodsReceivedNotificationMail($HOD, $requisitionItem->requisition));
                            Mail::to($emailRequestor)->queue(new GoodsReceivedNotificationMail($requestor, $requisitionItem->requisition));
                        }
                    } else {
                        break;
                    }
                }
            }
        }
    }

    public function getRequisitionDatatable($filters) {

        $search = $filters['search'];
        $order = $filters['order'];
        $by = $filters['by'];
        $paginate = $filters['paginate'];

        $typeNames = Requisition::ORDER_TYPE_ARRAY;

        $statuses = Requisition::STATUS_ARRAY;

        $requisition = Requisition::with([
                'rejectionNotes',
                'requisitionItems',
                'createdBy',
                'order' => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->when($search, function ($query) use ($search, $typeNames, $statuses) {
                $query->where('code', 'LIKE', "%{$search}%");
                $query->orWhere(function ($subQuery) use ($search, $typeNames) {
                    foreach ($typeNames as $typeId => $typeName) {
                        if (stripos($typeName, $search) !== false) {
                            $subQuery->orWhere('type', $typeId);
                        }
                    }
                });
                $query->orWhere(function ($subQuery) use ($search, $statuses) {
                    foreach ($statuses as $statusId => $status) {
                        if (stripos($status, $search) !== false) {
                            $subQuery->orWhere('status', $statusId);
                        }
                    }
                });
                $query->orWhereHas('createdBy', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'LIKE', "%{$search}%");
                });
            })->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $requisition->appends($queryString);
        return $requisition;
    }
}
