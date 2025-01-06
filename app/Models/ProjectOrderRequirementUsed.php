<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectOrderRequirementUsed extends Model
{
    use HasFactory;

    protected $table = 'project_order_requirement_used';

    protected $fillable = [
        'project_order_id',
        'project_order_requirement_id',
        'requisition_id',
        'requisition_item_id',
        'project_quotation_id',
        'project_quotation_vehicle_part_id',
        'quantity',
    ];

    protected static function booted()
    {
        static::created(function ($requirementUsed) {
            $requirementUsed->projectOrder->update(['quotation_generated_after_update' => true]);
        });

        static::updated(function ($requirementUsed) {
            $requirementUsed->projectOrder->update(['quotation_generated_after_update' => true]);
        });
    }

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }

    public function requisitionItem(): BelongsTo
    {
        return $this->belongsTo(RequisitionItem::class, 'requisition_item_id');
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(ProjectOrderRequirement::class, 'project_order_requirement_id');
    }

    public function projectOrder(): BelongsTo
    {
        return $this->belongsTo(ProjectOrder::class, 'project_order_id');
    }

    public function projectQuotation(): BelongsTo
    {
        return $this->belongsTo(ProjectQuotation::class, 'project_quotation_id');
    }

    public function projectQuotationVehiclePart(): BelongsTo
    {
        return $this->belongsTo(ProjectQuotationVehiclePart::class, 'project_quotation_vehicle_part_id');
    }
}
