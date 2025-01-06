<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contractor extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'tailgate_supplier',
        'tailgate_completion',
        'spray_painting_supplier',
        'spray_painting_completion',
        'confirmation',
    ];

    protected $casts = [
        'confirmation' => 'array',
    ];

    public function salesOrderECO(): BelongsTo
    {
        return $this->belongsTo(SalesOrderECO::class, 'sales_order_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ContractorItem::class, 'contractor_id');
    }
}
