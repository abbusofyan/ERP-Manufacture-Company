<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requisition extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'status',
        'type',
        'is_urgent',
        'note',
        'required_date',
        'created_by',
        'approved_by',
        'send_approval_to',
        'requisitionable_type',
        'requisitionable_id',
        'service_order_id',
        'requested_by',
        'department'
    ];

    const PENDING_STATUS = 1;
    const APPROVED_STATUS = 2;
    const REJECTED_STATUS = 3;
    const DRAFT_STATUS = 4;
    const COMPLETED_STATUS = 5;
    const VOID_STATUS = 6;

    const STATUS_ARRAY = [
        self::PENDING_STATUS => 'Pending For Approval',
        self::APPROVED_STATUS => 'Approved',
        self::REJECTED_STATUS => 'Rejected',
        self::DRAFT_STATUS => 'Draft',
        self::COMPLETED_STATUS => 'Completed',
        self::VOID_STATUS => 'Void',
    ];

    const STATUS_CLASS_ARRAY = [
        self::PENDING_STATUS => 'orange',
        self::APPROVED_STATUS => 'green',
        self::REJECTED_STATUS => 'danger',
        self::DRAFT_STATUS => 'gray',
        self::COMPLETED_STATUS => 'green',
        self::VOID_STATUS => 'danger',
    ];

    const CONSUMABLES_TYPE = 1;
    const OTHERS_TYPE = 2;
    const PRODUCTION_ORDER_TYPE = 3;
    const PROJECT_ORDER_TYPE = 4;
    // const QUOTATION_TYPE = 5;
    // const QUOTATION_FOR_DISINFECT_TYPE = 6;
    const SERVICE_ORDER_TYPE = 7;

    const ORDER_TYPE_ARRAY = [
        self::CONSUMABLES_TYPE => 'Consumables',
        self::OTHERS_TYPE => 'Others',
        self::PRODUCTION_ORDER_TYPE => 'Production Order',
        self::PROJECT_ORDER_TYPE => 'Project Order',
        // self::QUOTATION_TYPE => 'Quotation',
        // self::QUOTATION_FOR_DISINFECT_TYPE => 'Quotation for Disinfect',
        self::SERVICE_ORDER_TYPE => 'Service Order',
    ];

    const ORDER_TYPE_MODEL_ARRAY = [
        self::CONSUMABLES_TYPE => null,
        self::OTHERS_TYPE => null,
        self::PRODUCTION_ORDER_TYPE => 'App\Models\ProductionOrder',
        self::PROJECT_ORDER_TYPE => 'App\Models\ProjectOrder',
        // self::QUOTATION_TYPE => 'App\Models\Quotation',
        // self::QUOTATION_FOR_DISINFECT_TYPE => 'App\Models\Quotation',
        self::SERVICE_ORDER_TYPE => 'App\Models\ServiceOrder',
    ];

    const ORDER_TYPE_ARRAY_SELECT = [
        [
            'id' => self::CONSUMABLES_TYPE,
            'text' => self::ORDER_TYPE_ARRAY[self::CONSUMABLES_TYPE],
        ],
        [
            'id' => self::OTHERS_TYPE,
            'text' => self::ORDER_TYPE_ARRAY[self::OTHERS_TYPE],
        ],
        [
            'id' => self::PRODUCTION_ORDER_TYPE,
            'text' => self::ORDER_TYPE_ARRAY[self::PRODUCTION_ORDER_TYPE],
        ],
        [
            'id' => self::PROJECT_ORDER_TYPE,
            'text' => self::ORDER_TYPE_ARRAY[self::PROJECT_ORDER_TYPE],
        ],
        // [
        //     'id' => self::QUOTATION_TYPE,
        //     'text' => self::ORDER_TYPE_ARRAY[self::QUOTATION_TYPE],
        // ],
        // [
        //     'id' => self::QUOTATION_FOR_DISINFECT_TYPE,
        //     'text' => self::ORDER_TYPE_ARRAY[self::QUOTATION_FOR_DISINFECT_TYPE],
        // ],
        [
            'id' => self::SERVICE_ORDER_TYPE,
            'text' => self::ORDER_TYPE_ARRAY[self::SERVICE_ORDER_TYPE],
        ],
        // [
        //     'id' => self::PROJECT_ORDER,
        //     'text' => self::ORDER_TYPE_ARRAY[self::PROJECT_ORDER],
        // ]
    ];

    protected $appends = [
        'status_text',
        'status_class',
        'type_text',
        'order_type_model_text',
        'statuses',
        'status_classes',
        'order_types'
    ];

    protected static function booted()
    {
        static::created(function ($requisition) {
            $requisition->update([
                'code' => 'REQ' . str_pad($requisition->id, 4, '0', STR_PAD_LEFT)
            ]);
        });
    }

    public function getTypeTextAttribute(): string
    {
        return self::ORDER_TYPE_ARRAY[$this->type] ?? $this->type;
    }

    public function getStatusTextAttribute(): string
    {
        return self::STATUS_ARRAY[$this->status];
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS_CLASS_ARRAY[$this->status];
    }

    public function getOrderTypeModelTextAttribute()
    {
        return self::ORDER_TYPE_MODEL_ARRAY[$this->type];
    }

    public function getStatusesAttribute(): array
    {
        return self::STATUS_ARRAY;
    }

    public function getOrderTypesAttribute(): array
    {
        return self::ORDER_TYPE_ARRAY;
    }

    public function getStatusClassesAttribute(): array
    {
        return self::STATUS_CLASS_ARRAY;
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function requisitionItems()
    {
        return $this->hasMany(RequisitionItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class, 'service_order_id');
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function requisitionable()
    {
        return $this->morphTo();
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_requisitions', 'requisition_id', 'order_id')
                    ->withPivot('order_item_id', 'requisition_item_id')
                    ->withTimestamps();
    }

    public function rejectionNotes() {
        return $this->hasMany(RequisitionRejectNote::class)->orderBy('id', 'desc');
    }

    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            RequisitionItem::class,
            'requisition_id', // Foreign key on requisition_items
            'id',             // Foreign key on products
            'id',             // Local key on requisitions
            'product_id'      // Local key on requisition_items
        );
    }
}
