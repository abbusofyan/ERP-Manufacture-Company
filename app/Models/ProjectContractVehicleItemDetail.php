<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectContractVehicleItemDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_contract_pricing_id',
        'vehicle_id',
        'vehicle_license_plate',
        'refrigeration_model',
        'date_of_install',
        'standby_unit',
    ];

    protected $casts = [
        'standby_unit' => 'boolean',
    ];

    public function pricing(): BelongsTo
    {
        return $this->belongsTo(ProjectContractPricing::class, 'project_contract_pricing_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}
