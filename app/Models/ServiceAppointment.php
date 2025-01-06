<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceAppointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cmp_number',
        'customer_feedback',
        'date_of_appointment',
        'service_order_type',
        'customer_id',
        'vehicle_id',
        'pic_first_name',
        'pic_last_name',
        'pic_title',
        'pic_country_code',
        'pic_phone_number',
        'pic_email',
        'remark',
        'status',
        ' ',
        'warranty_contract_id',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    const ACTIVE_PREFIX = 'SAA';
    const DRAFT_PREFIX = 'SAD';

    const TYPES = [
        [
            'id' => 'Refrigeration System Repair',
            'text' => 'Refrigeration System Repair',
            'is_cmp' => false,
        ],
        [
            'id' => 'Insulation Box Repair',
            'text' => 'Insulation Box Repair',
            'is_cmp' => false,
        ],
        [
            'id' => 'CMP',
            'text' => 'CMP',
            'is_cmp' => true,
        ],
    ];

    const STATUS_ACTIVE = 1;
    const STATUS_DRAFT = 2;
    const STATUS_VOID = 3;
    const STATUS_CONVERTED = 4;

    const STATUS_TEXT_ARRAY = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_VOID => 'Void',
        self::STATUS_CONVERTED => 'Converted',
    ];

    const STATUS_CLASS_ARRAY = [
        self::STATUS_DRAFT => 'gray',
        self::STATUS_VOID => 'danger',
        self::STATUS_CONVERTED => 'green',
    ];

    protected $appends = [
        'status_text',
        'status_class',
        'is_expired',
    ];

    /**
     * Check if the appointment is expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        $appointmentDate = $this->date_of_appointment instanceof Carbon ? $this->date_of_appointment : Carbon::parse($this->date_of_appointment);
        if ($appointmentDate->isFuture() || $appointmentDate->isToday()) {
            return false;
        }

        return true;
    }

    public function getIsExpiredAttribute(): string
    {
        return self::isExpired();
    }

    public function getStatusTextAttribute(): string
    {
        if ($this->status == self::STATUS_ACTIVE) {
            if (!$this->isExpired()) {
                return 'Upcoming';
            } else {
                return 'Expired';
            }
        }

        return self::STATUS_TEXT_ARRAY[$this->status] ?? 'Unknown';
    }

    public function getStatusClassAttribute(): string
    {
        if ($this->status == self::STATUS_ACTIVE) {
            if (!$this->isExpired()) {
                return 'orange';
            } else {
                return 'danger';
            }
        }

        return self::STATUS_CLASS_ARRAY[$this->status] ?? 'Unknown';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function maintains()
    {
        return $this->hasMany(ServiceMaintain::class, 'service_appointment_id');
    }

    /**
     * Relationship with the sales order (warranty_sale_order_eco_id)
     */
    public function warrantySaleOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrderECO::class, 'warranty_sale_order_eco_id');
    }

    /**
     * Relationship with the service contract (warranty_contract_id)
     */
    public function warrantyContract(): BelongsTo
    {
        return $this->belongsTo(ServiceContract::class, 'warranty_contract_id');
    }
}
