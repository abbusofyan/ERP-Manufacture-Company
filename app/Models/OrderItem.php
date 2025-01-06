<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'order_id',
        'vendor_id',
        'product_id',
        'product_name',
        'quantity',
        'received_qty',
        'canceled_qty',
        'unit_price',
        'gst_id',
        'gst_amount',
        'total',
        'status',
    ];

    const STATUS_PENDING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_PARTIALLY_RECEIVED = 3;
    const STATUS_RECEIVED = 4;
    const STATUS_CANCELED = 5;

    const STATUS_TEXT_ARRAY = [
        self::STATUS_PENDING => 'Pending Receive',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_PARTIALLY_RECEIVED => 'Partially Receive',
        self::STATUS_RECEIVED => 'Received',
        self::STATUS_CANCELED => 'Canceled',
    ];

    const STATUS_CLASS_ARRAY = [
        self::STATUS_PENDING => 'orange',
        self::STATUS_COMPLETED => 'green',
        self::STATUS_PARTIALLY_RECEIVED => 'primary',
        self::STATUS_RECEIVED => 'green',
        self::STATUS_CANCELED => 'danger',
    ];

    protected $appends = [
        'status_text',
        'status_class',
    ];

    public function getStatusTextAttribute(): ?string
    {
        return $this->status ? self::STATUS_TEXT_ARRAY[$this->status] : null;
    }

    public function getStatusClassAttribute(): ?string
    {
        return $this->status ? self::STATUS_CLASS_ARRAY[$this->status] : null;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function gst() {
        return $this->belongsTo(GST::class);
    }
}
