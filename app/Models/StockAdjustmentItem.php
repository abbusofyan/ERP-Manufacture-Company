<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustmentItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'stock_adjustment_id',
        'product_id',
        'store_id',
        'adjustment',
        'adjustment_qty',
        'before_qty',
        'after_qty',
        'price',
    ];

    public function stockAdjustment(): BelongsTo
    {
        return $this->belongsTo(StockAdjustment::class,'stock_adjustment_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function store() {
        return $this->belongsTo(Store::class);
    }
}
