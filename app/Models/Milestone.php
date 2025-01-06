<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Milestone extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['sales_order_id', 'delivery_date'];

    public function salesOrderECO(): BelongsTo
    {
        return $this->belongsTo(SalesOrderECO::class, 'sales_order_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MilestoneItem::class);
    }
}
