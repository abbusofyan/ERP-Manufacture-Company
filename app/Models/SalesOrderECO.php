<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrderECO extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'sales_orders_eco';

    protected $fillable = [
        'rs_id',
        'schedule_comments',
        'vehicle_receive_date',
        'actual_completion_date',
        'vehicle_id',
        'engine_serial',
        'evaporator_serial',
        'refrigeration_unit_serial',
        'nea_serial',
        'condenser_serial',
        'standby_unit',
        'status',
    ];

    protected $casts = [
        'engine_serial' => 'json',
        'evaporator_serial' => 'json',
        'condenser_serial' => 'json',
    ];

    const STATUS_PENDING = 1;
    const STATUS_CONFIRMED = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_CANCELED = 5;

    const STATUS_TEXT_ARRAY = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELED => 'Canceled',
    ];

    const STATUS_CLASS_ARRAY = [
        self::STATUS_PENDING => 'orange',
        self::STATUS_CONFIRMED => 'green',
        self::STATUS_COMPLETED => 'green',
        self::STATUS_CANCELED => 'danger',
    ];

    protected $appends = [
        'status_text',
        'status_class',
    ];

    public function getStatusTextAttribute(): string
    {
        return $this->status ? self::STATUS_TEXT_ARRAY[$this->status] : '';
    }

    public function getStatusClassAttribute(): string
    {
        return $this->status ? self::STATUS_CLASS_ARRAY[$this->status] : '';
    }

    public function refrigerationSale(): BelongsTo
    {
        return $this->belongsTo(RefrigerationSale::class, 'rs_id');
    }

    public function mineStones(): HasMany
    {
        return $this->hasMany(Milestone::class, 'sales_order_id');
    }

    public function productionWorkOrder(): HasOne
    {
        return $this->hasOne(ProductionWorkOrder::class, 'sales_order_id');
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'sales_order_vehicle', 'sales_order_id', 'vehicle_id');
    }

    public function contractor(): HasOne
    {
        return $this->hasOne(Contractor::class, 'sales_order_id');
    }
}
