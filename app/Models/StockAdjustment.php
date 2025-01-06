<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustment extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'stock_adjustment_type_id',
        'code',
        'reason',
        'created_by',
        'updated_by',
        'approved_by',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StockAdjustmentDocument::class);
    }

    public function staff(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'staff_id');
    }

    public function approved_by(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'approved_by');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(StockAdjustmentType::class, 'stock_adjustment_type_id');
    }
}
