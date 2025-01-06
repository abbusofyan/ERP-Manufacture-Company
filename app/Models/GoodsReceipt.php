<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class GoodsReceipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'code',
        'requester_name',
        'is_return',
        'qr_path',
        'status',
        'do_number',
        'updated_by'
    ];

    const STATUS_PENDING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_PARTIALLY_RECEIVED = 3;
    const STATUS_RECEIVED = 4;
    const STATUS_VOID = 5;
    const STATUS_CANCELED = 6;

    const STATUS_TEXT_ARRAY = [
        self::STATUS_PENDING => 'Pending Receive',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_PARTIALLY_RECEIVED => 'Partially Received',
        self::STATUS_RECEIVED => 'Received',
        self::STATUS_VOID => 'Void',
        self::STATUS_CANCELED => 'Canceled',
    ];

    const STATUS_CLASS_ARRAY = [
        self::STATUS_PENDING => 'orange',
        self::STATUS_COMPLETED => 'primary',
        self::STATUS_PARTIALLY_RECEIVED => 'primary',
        self::STATUS_RECEIVED => 'green',
        self::STATUS_VOID => 'danger',
        self::STATUS_CANCELED => 'danger',
    ];

    protected $appends = [
        'status_text',
        'status_class',
    ];

    protected static function booted()
    {
        static::updating(function ($goodsReceipt) {
            if (Auth::check()) {
                $goodsReceipt->updated_by = Auth::id();
            }
        });
    }

    public function getStatusTextAttribute(): ?string
    {
        return $this->status ? self::STATUS_TEXT_ARRAY[$this->status] : null;
    }

    public function getStatusClassAttribute(): ?string
    {
        return $this->status ? self::STATUS_CLASS_ARRAY[$this->status] : null;
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    public function documents()
    {
        return $this->hasMany(GoodsReceiptDocument::class);
    }

    public function history() {
        return $this->hasMany(GoodsReceiptHistory::class);
    }
}
