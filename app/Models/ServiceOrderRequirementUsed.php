<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrderRequirementUsed extends Model
{
    use HasFactory;

    protected $table = 'service_order_requirement_used';

    protected $fillable = [
        'service_order_id',
        'service_order_requirement_id',
        'requisition_id',
        'requisition_item_id',
        'service_quotation_id',
        'service_quotation_vehicle_part_id',
        'quantity',
    ];

    protected static function booted()
    {
        static::created(function ($requirementUsed) {
            $requirementUsed->serviceOrder->update(['quotation_generated_after_update' => true]);
        });

        static::updated(function ($requirementUsed) {
            $requirementUsed->serviceOrder->update(['quotation_generated_after_update' => true]);
        });
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }

    public function requisitionItem(): BelongsTo
    {
        return $this->belongsTo(RequisitionItem::class, 'requisition_item_id');
    }

    public function requirement()
    {
        return $this->belongsTo(ServiceOrderRequirement::class, 'service_order_requirement_id');
    }

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class, 'service_order_id');
    }

    public function serviceQuotation()
    {
        return $this->belongsTo(ServiceQuotation::class, 'service_quotation_id');
    }

    public function serviceQuotationVehiclePart()
    {
        return $this->belongsTo(ServiceQuotationVehiclePart::class, 'service_quotation_vehicle_part_id');
    }
}
