<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefrigerationSaleHeaderType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'rs_header_id',
        'box',
        'description',
        'quantity',
    ];

    const TYPE_ASSEMBLY = 1;
    const TYPE_SINGLE_PRODUCT = 2;

    const TYPE_ARR = [
        [
            'id' => self::TYPE_ASSEMBLY,
            'text' => 'Assembly'
        ],
        [
            'id' => self::TYPE_SINGLE_PRODUCT,
            'text' => 'Single Product'
        ],
    ];

    public function items(): HasMany
    {
        return $this->hasMany(RefrigerationSaleHeaderTypeItem::class, 'rs_header_type_id');
    }

    public function header(): BelongsTo
    {
        return $this->belongsTo(RefrigerationSaleHeader::class, 'rs_header_id');
    }
}
