<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_contract_number',
        'start_duration_date',
        'end_duration_date',
        'contract_period',
        'contract_created_date',
        'customer_reference_no',
        'value',
        'sub_total',
        'discount',
        'tax_rate',
        'tax_amount',
        'total',
        'term_of_payment',
        'customer_id',
        'pic_first_name',
        'pic_last_name',
        'pic_title',
        'pic_country_code',
        'pic_phone_number',
        'pic_email',
        'remark',
        'status',
        'signed_image_url',
        'file_url',
    ];

    protected $appends = [
        'status_text', 'status_class', 'full_signed_image_url', 'can_early_terminate',
    ];

    const PREFIX = 'CTR';

    const TERM_OF_PAYMENTS = [
        [
            'id' => 'Monthly',
            'text' => 'Monthly',
        ],
        [
            'id' => 'Quarterly',
            'text' => 'Quarterly',
        ],
        [
            'id' => 'Half yearly payment',
            'text' => 'Half yearly payment',
        ],
        [
            'id' => 'Yearly',
            'text' => 'Yearly',
        ],
        [
            'id' => 'Full Payment',
            'text' => 'Full Payment',
        ],
    ];

    const STATUS_DRAFT = 0;
    const STATUS_PENDING = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_VOID = 3;
    const STATUS_EARLY_TERMINATION = 4;
    const STATUS_COMPLETED = 5;

    const STATUS = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ACCEPTED => 'Accepted',
        self::STATUS_VOID => 'Void',
        self::STATUS_EARLY_TERMINATION => 'Early Termination',
        self::STATUS_COMPLETED => 'Completed',
    ];

    const STATUS_SELECT2 = [
        [
            'id' => self::STATUS_PENDING,
            'text' => 'Pending',
        ],
        [
            'id' => self::STATUS_ACCEPTED,
            'text' => 'Accepted',
        ],
        [
            'id' => self::STATUS_VOID,
            'text' => 'Void',
        ],
        [
            'id' => self::STATUS_EARLY_TERMINATION,
            'text' => 'Early Termination',
        ],
        [
            'id' => self::STATUS_COMPLETED,
            'text' => 'Completed',
        ],
    ];

    const STATUS_CLASS = [
        self::STATUS_DRAFT => 'gray',
        self::STATUS_PENDING => 'orange',
        self::STATUS_ACCEPTED => 'primary',
        self::STATUS_VOID => 'gray',
        self::STATUS_EARLY_TERMINATION => 'danger',
        self::STATUS_COMPLETED => 'green',
    ];

    public function getStatusTextAttribute(): string
    {
        return self::STATUS[$this->status];
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS_CLASS[$this->status];
    }

    public function getFullSignedImageUrlAttribute(): ?string
    {
        return $this->signed_image_url ? url('storage/' . $this->signed_image_url) : null;
    }

    public function getCanEarlyTerminateAttribute(): bool
    {
        $today = Carbon::now();
        $endDate = Carbon::parse($this->end_duration_date);

        if ($today->diffInDays($endDate, false) <= 15) {
            return true;
        }

        return false;
    }

    public function pricing(): HasMany
    {
        return $this->hasMany(ProjectContractPricing::class, 'project_contract_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(ProjectContractInvoice::class, 'project_contract_id');
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'project_contract_vehicle');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function maintains(): HasMany
    {
        return $this->hasMany(ProjectMaintain::class, 'project_contract_id');
    }

    public function vehicleItemDetails(): HasManyThrough
    {
        return $this->hasManyThrough(ProjectContractVehicleItemDetail::class, ProjectContractPricing::class, 'project_contract_id', 'project_contract_pricing_id');
    }
}
