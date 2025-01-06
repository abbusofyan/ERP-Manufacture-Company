<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceiptItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'goods_receipt_id',
        'product_id',
        'product_name',
        'uom',
        'quantity',
        'receive_quantity',
        'return_qty',
        'is_return',
        'status'
    ];

    const STATUS_PENDING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_PARTIALLY_RECEIVED = 3;
    const STATUS_CANCELED = 4;

    const STATUS_TEXT_ARRAY = [
        self::STATUS_PENDING => 'Pending Receive',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_PARTIALLY_RECEIVED => 'Partially Receive',
        self::STATUS_CANCELED => 'Canceled',
    ];

    const STATUS_CLASS_ARRAY = [
        self::STATUS_PENDING => 'orange',
        self::STATUS_COMPLETED => 'green',
        self::STATUS_PARTIALLY_RECEIVED => 'primary',
        self::STATUS_CANCELED => 'danger',
    ];

    protected $appends = [
        'status_text',
        'status_class',
    ];

    public function getStatusTextAttribute(): string
    {
        return self::STATUS_TEXT_ARRAY[$this->status];
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS_CLASS_ARRAY[$this->status];
    }

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
