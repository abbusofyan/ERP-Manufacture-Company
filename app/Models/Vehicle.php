<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_plate',
        'customer_id',
        'type',
        'end_user',
        'vehicle_voltage',
        'contact_no',
        'chassis_no',
        'chassis_delivery',
        'vehicle_make',
        'model',
        'vehicle_class',
        'type_of_plate',
        'refrigeration_serial_number',
        'other_info',
        'current_mileage',
        'mileage_date',
        'warranty_mileage',
        'warranty_end_date',
    ];

    protected $appends = [
        'warranty_status',
        'text',
        'nearest_service_contract',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function serviceContracts(): BelongsToMany
    {
        return $this->belongsToMany(ServiceContract::class, 'service_contract_vehicle');
    }

    public function nearestServiceContract(): ?ServiceContract
    {
        return $this->serviceContracts()
            ->where('end_duration_date', '>=', Carbon::now())
            ->orderBy('end_duration_date', 'asc')
            ->first();
    }

    //Sale order eco
    public function salesOrders(): BelongsToMany
    {
        return $this->belongsToMany(SalesOrderECO::class, 'sales_order_vehicle', 'vehicle_id', 'sales_order_id');
    }

    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class, 'vehicle_id');
    }

    /**
     * Get the warranty status of the vehicle.
     *
     * @return string
     */
    public function getWarrantyStatusAttribute(): string
    {
        $currentDate = Carbon::now();

        if ($this->warranty_mileage == null || $this->current_mileage == null || $this->warranty_end_date == null) {
            return 'Warranty not specified';
        }

        // Check mileage-based warranty
        $mileageWarranty = $this->warranty_mileage && intval($this->current_mileage) <= intval($this->warranty_mileage);

        // Check date-based warranty
        $dateWarranty = $this->warranty_end_date && $currentDate->lessThanOrEqualTo(Carbon::parse($this->warranty_end_date));

        if ($mileageWarranty && $dateWarranty) {
            return 'Under Warranty';
        } elseif ($mileageWarranty && !$dateWarranty) {
            return 'Warranty Expired (Date)';
        } elseif (!$mileageWarranty && $dateWarranty) {
            return 'Warranty Expired (Date)';
        } else {
            return 'Warranty not specified';
        }
    }

    public function getTextAttribute(): string
    {
        return $this->license_plate;
    }

    /**
     * Accessor for nearest_service_contract.
     *
     * @return \App\Models\ServiceContract|null
     */
    public function getNearestServiceContractAttribute(): ?ServiceContract
    {
        return $this->nearestServiceContract();
    }
}
