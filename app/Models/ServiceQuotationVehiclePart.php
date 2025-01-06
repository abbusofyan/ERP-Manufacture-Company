<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceQuotationVehiclePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_quotation_id',
        'service_invoice_id',
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
        'is_hide' => 'boolean',  // Cast as boolean
    ];

    protected static function booted()
    {
        static::created(function ($part) {
            $part->serviceQuotation->updatePricing();
        });

        static::updated(function ($part) {
            $part->serviceQuotation->updatePricing();
        });

        static::deleted(function ($part) {
            $part->serviceQuotation->updatePricing();
        });
    }

    public function serviceQuotation()
    {
        return $this->belongsTo(ServiceQuotation::class, 'service_quotation_id');
    }

    public function storageItem()
    {
        return $this->belongsTo(StorageItem::class, 'storage_item_id');
    }
}
