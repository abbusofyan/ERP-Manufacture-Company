<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefrigerationSaleHeaderTypeItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'rs_header_type_id',
        'type',
        'product_id',
        'assembly_id',
        'uom',
        'quantity',
        'unit_price',
        'sub_total',
        'discount_amount',
        'discount_value',
        'discount_type',
        'tax_code',
        'tax_value',
        'tax_amount',
        'total',
        'description',
        'misc_description',
        'is_foc',
        'hide',
        'quantity_on_stock',
    ];

    protected $casts = [
        'is_foc' => 'boolean',
        'hide' => 'boolean',
        'discount_amount' => 'float',
        'quantity_on_stock' => 'integer',
        'tax_value' => 'float',
        'tax_amount' => 'float',
        'unit_price' => 'float',
        'sub_total' => 'float',
        'total' => 'float',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(RefrigerationSaleHeaderType::class, 'rs_header_type_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function assembly(): BelongsTo
    {
        return $this->belongsTo(Assembly::class, 'assembly_id');
    }
}
