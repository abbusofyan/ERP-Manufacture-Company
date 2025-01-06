<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseGoodsReturn extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS_PENDING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_PARTIALLY_RECEIVED = 3;
    const STATUS_RECEIVED = 4;
    const STATUS_VOID = 5;
    const STATUS_CANCELED = 6;

    const STATUS_TEXT_ARRAY = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_VOID => 'Void',
        self::STATUS_CANCELED => 'Canceled',
    ];

    const STATUS_CLASS_ARRAY = [
        self::STATUS_PENDING => 'orange',
        self::STATUS_COMPLETED => 'green',
        self::STATUS_VOID => 'danger',
        self::STATUS_CANCELED => 'danger',
    ];

    protected $appends = [
        'status_text',
        'status_class',
    ];

    protected static function booted()
    {
        static::created(function ($goodsReturn) {
            $goodsReturn->update([
                'code' => 'GRS' . str_pad($goodsReturn->id, 4, '0', STR_PAD_LEFT)
            ]);
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

    public function goodsReceipt() {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }
}
