<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceMaintain extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'service_appointment_id',
        'service_contract_id',
        'date',
        'status',
        'is_active',
        'is_draft',
    ];

    protected $appends = ['status_text', 'status_class'];

    const STATUS_PENDING = 'Pending Service';
    const STATUS_IN_SERVICE = 'In Service';
    const STATUS_SERVICED = 'Serviced';
    const STATUS_OVERDUE = 'Service Overdue';

    const STATUS_CLASS = [
        self::STATUS_PENDING => 'orange',
        self::STATUS_IN_SERVICE => 'primary',
        self::STATUS_SERVICED => 'green',
        self::STATUS_OVERDUE => 'danger',
    ];

    public function getStatusTextAttribute(): string
    {
        $currentMonth = Carbon::now()->startOfMonth();

        if (Carbon::parse($this->date)->lt($currentMonth) && $this->status !== self::STATUS_SERVICED) {
            return self::STATUS_OVERDUE;
        }

        return $this->status;
    }

    public function getStatusClassAttribute(): string
    {
        $currentMonth = Carbon::now()->startOfMonth();

        if (Carbon::parse($this->date)->lt($currentMonth) && $this->status !== self::STATUS_SERVICED) {
            return self::STATUS_CLASS[self::STATUS_OVERDUE];
        }

        return self::STATUS_CLASS[$this->status];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function serviceAppointment()
    {
        return $this->belongsTo(ServiceAppointment::class, 'service_appointment_id');
    }

    public function serviceContract()
    {
        return $this->belongsTo(ServiceContract::class, 'service_contract_id');
    }
}
