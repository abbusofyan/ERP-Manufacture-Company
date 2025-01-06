<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefrigerationSaleHeader extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'rs_id',
        'header_name',
        'vehicle_id',
        'vehicle_license_plate',
        'vehicle_chassis_no',
        'vehicle_voltage',
        'vehicle_make',
        'vehicle_model',
        'vehicle_class',
        'vehicle_type_of_plate',
        'vehicle_mileage',
        'vehicle_running_hours',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function types(): HasMany
    {
        return $this->hasMany(RefrigerationSaleHeaderType::class, 'rs_header_id');
    }

    public function refrigeration_sale(): BelongsTo
    {
        return $this->belongsTo(RefrigerationSale::class, 'rs_id');
    }
}
