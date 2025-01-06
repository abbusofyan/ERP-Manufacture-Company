<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceContractVehicleItemDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_contract_pricing_id',
        'vehicle_id',
        'vehicle_license_plate',
        'refrigeration_model',
        'date_of_install',
        'standby_unit',
    ];

    protected $casts = [
        'standby_unit' => 'boolean',
    ];

    /**
     * Get the related service contract pricing.
     */
    public function pricing(): BelongsTo
    {
        return $this->belongsTo(ServiceContractPricing::class, 'service_contract_pricing_id');
    }

    /**
     * Get the related vehicle.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}
