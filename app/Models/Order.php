<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'status',
        'code',
        'vendor_id',
        'remark',
        'purchase_date',
        'delivery_date',
        'payment_method',
        'terms',
        'currency',
        'criterion',
        'sub_total',
        'discount',
        'freight',
        'gst',
        'rounding',
        'total',
        'created_by',
        'updated_by'
    ];

    const STATUS_PENDING = 1;
    const STATUS_SENT_TO_SUP = 2;
    const STATUS_CONFIRMED = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_CANCELED = 5;
    const STATUS_REJECTED = 6;

    const STATUS_TEXT_ARRAY = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_SENT_TO_SUP => 'Sent to Supplier',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELED => 'Canceled',
        self::STATUS_REJECTED => 'Rejected',
    ];

    const STATUS_CLASS_ARRAY = [
        self::STATUS_PENDING => 'orange',
        self::STATUS_SENT_TO_SUP => 'green',
        self::STATUS_CONFIRMED => 'green',
        self::STATUS_COMPLETED => 'primary',
        self::STATUS_CANCELED => 'danger',
        self::STATUS_REJECTED => 'danger',
    ];

    protected $appends = [
        'status_text',
        'status_class',
        'statuses'
    ];

    protected static function booted()
    {
        static::created(function ($order) {
            $order->update([
                'code' => 'PO' . str_pad($order->id, 4, '0', STR_PAD_LEFT)
            ]);
        });

        static::updating(function ($order) {
            if (Auth::check()) {
                $order->updated_by = Auth::id();
            }
        });
    }

    public function getStatusesAttribute(): array
    {
        return self::STATUS_TEXT_ARRAY;
    }

    public function getStatusTextAttribute(): ?string
    {
        return $this->status ? self::STATUS_TEXT_ARRAY[$this->status] : null;
    }

    public function getStatusClassAttribute(): ?string
    {
        return $this->status ? self::STATUS_CLASS_ARRAY[$this->status] : null;
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function requisitions()
    {
        return $this->belongsToMany(Requisition::class, 'order_requisitions', 'order_id', 'requisition_id')
                    ->withPivot('order_item_id', 'requisition_item_id')
                    ->withTimestamps();
    }

    public function goodsReceipt() {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function itemMovements()
    {
        return $this->morphMany(ItemMovement::class, 'transaction');
    }
}
