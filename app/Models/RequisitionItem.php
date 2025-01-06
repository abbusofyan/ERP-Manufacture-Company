<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequisitionItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'requisition_id',
        'product_id',
        'product_name',
        'uom',
        'store_id',
        'status',
        'requested_qty',
        'pending_order_qty',
        'ordered_qty',
        'committed_qty',
        'pending_issue_qty',
        'issued_qty',
        'returned_qty',
        'remark',
        'file_url',
        'required_date',
        'checked_by',
        'notes'
    ];

    const PENDING_STATUS = 1;
    const APPROVED_STATUS = 2;
    const COMPLETED_STATUS = 2;
    const REJECTED_STATUS = 3;
    const PENDING_CONFIRM_ORDER_STATUS = 4;
    const CANCELED_STATUS = 5;

    const STATUS_ARRAY = [
        self::PENDING_STATUS => 'Pending',
        self::APPROVED_STATUS => 'Approved',
        self::COMPLETED_STATUS => 'Completed',
        self::REJECTED_STATUS => 'Rejected',
        self::PENDING_CONFIRM_ORDER_STATUS => 'Pending',
        self::CANCELED_STATUS => 'Canceled',
    ];

    const STATUS_CLASS_ARRAY = [
        self::PENDING_STATUS => 'orange',
        self::APPROVED_STATUS => 'green',
        self::COMPLETED_STATUS => 'green',
        self::REJECTED_STATUS => 'danger',
        self::PENDING_CONFIRM_ORDER_STATUS => 'danger',
        self::CANCELED_STATUS => 'danger',
    ];

    protected $appends = [
        'status_text',
        'status_class',
    ];

    public function getStatusTextAttribute(): string
    {
        return self::STATUS_ARRAY[$this->status];
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS_CLASS_ARRAY[$this->status];
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the GINItems associated with the RequisitionItem.
     */
    public function ginItems()
    {
        return $this->hasMany(GINItem::class, 'requisition_item_id', 'id');
    }

    public function checkedBy() {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
