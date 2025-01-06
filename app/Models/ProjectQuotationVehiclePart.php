<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectQuotationVehiclePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_quotation_id',
        'project_invoice_id',
        'storage_item_id',

        'name',
        'quantity',

        'tax_code',
        'tax_amount',
        'discount',
        'discount_amount',
        'subtotal_amount',
        'total_amount',

        'is_foc',
        'description',
        'misc_description',
        'is_hide',
    ];

    protected $casts = [
        'is_foc' => 'boolean',
        'is_hide' => 'boolean',
    ];

    protected static function booted()
    {
        static::created(function ($part) {
            $part->projectQuotation->updatePricing();
        });

        static::updated(function ($part) {
            $part->projectQuotation->updatePricing();
        });

        static::deleted(function ($part) {
            $part->projectQuotation->updatePricing();
        });
    }

    public function projectQuotation(): BelongsTo
    {
        return $this->belongsTo(ProjectQuotation::class, 'project_quotation_id');
    }

    public function storageItem(): BelongsTo
    {
        return $this->belongsTo(StorageItem::class, 'storage_item_id');
    }
}
