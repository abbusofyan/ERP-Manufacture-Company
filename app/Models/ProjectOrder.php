<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_maintain_id',
        'converted_project_appointment_id',
        'code',
        'customer_feedback',
        'stage',
        'project_quotation_id',
        'technician_id',
        'customer_id',
        'confirmed_by',
        'current_project_order_process_id',
        'pic_first_name',
        'pic_last_name',
        'pic_title',
        'pic_country_code',
        'pic_phone_number',
        'pic_email',
        'project_order_type',
        'plan_start_date',
        'plan_complete_date',
        'est_complete_date',
        'delivery_date',
        'in_warranty',
        'come_back_job',
        'remark',
        'status',
        'warranty_sale_order_eco_id',
        'warranty_contract_id',
        'change_of_specs',
        'quotation_generated_after_update'
    ];

    protected $casts = [
        'status' => 'integer',
        'change_of_specs' => 'boolean',
        'quotation_generated_after_update' => 'boolean',
    ];

    protected $appends = [
        'status_text', 'status_class', 'warranty_text', 'warranty_class', 'total_process'
    ];

    const ACTIVE_PREFIX = 'SOA';
    const DRAFT_PREFIX = 'SOD';

    const TYPES = [
        [
            'id' => 'Refrigeration System Repair',
            'text' => 'Refrigeration System Repair',
        ],
        [
            'id' => 'Insulation Box Repair',
            'text' => 'Insulation Box Repair',
        ],
        [
            'id' => 'CMP',
            'text' => 'CMP',
        ],
        [
            'id' => 'CMP (temp for invalid cmp)',
            'text' => 'CMP (temp for invalid cmp)',
        ],
    ];

    const STATUS_PENDING = 1;
    const STATUS_DRAFT = 2;
    const STATUS_FIRST_CHECK = 3;
    const STATUS_IN_PROGRESS = 4;
    const STATUS_LAST_CHECK = 5;
    const STATUS_COLLECTION = 6;
    const STATUS_COMPLETE = 7;
    const STATUS_REOPEN_COMPLETE = 8;
    const STATUS_VOID = 9;

    const STATUS = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_FIRST_CHECK => 'First Check',
        self::STATUS_IN_PROGRESS => 'Work In Progress',
        self::STATUS_LAST_CHECK => 'Last Check',
        self::STATUS_COLLECTION => 'Collection',
        self::STATUS_COMPLETE => 'Complete',
        self::STATUS_REOPEN_COMPLETE => 'Reopen Complete',
        self::STATUS_VOID => 'Void',
    ];

    const STATUS_CLASS = [
        self::STATUS_PENDING => 'orange',
        self::STATUS_DRAFT => 'gray',
        self::STATUS_FIRST_CHECK => 'orange',
        self::STATUS_IN_PROGRESS => 'orange',
        self::STATUS_LAST_CHECK => 'orange',
        self::STATUS_COLLECTION => 'green',
        self::STATUS_COMPLETE => 'green',
        self::STATUS_REOPEN_COMPLETE => 'green',
        self::STATUS_VOID => 'danger',
    ];

    public function getStatusTextAttribute(): string
    {
        return self::STATUS[$this->status];
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS_CLASS[$this->status];
    }

    public function getWarrantyTextAttribute(): string
    {
        if ($this->in_warranty) {
            return 'In warranty';
        }

        return 'Out of Warranty';
    }

    public function getWarrantyClassAttribute(): string
    {
        if ($this->in_warranty) {
            return 'green';
        }

        return 'orange';
    }

    public function getTotalProcessAttribute(): int
    {
        return $this->projectOrderProcess()->count();
    }

    // Relationships
    public function maintain(): BelongsTo
    {
        return $this->belongsTo(ProjectMaintain::class, 'project_maintain_id');
    }

    public function projectQuotation(): BelongsTo
    {
        return $this->belongsTo(ProjectQuotation::class, 'project_quotation_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'project_order_vehicle');
    }

    public function currentProcess(): BelongsTo
    {
        return $this->belongsTo(ProjectOrderProcess::class, 'current_project_order_process_id');
    }

    public function vehicleParts(): HasMany
    {
        return $this->hasMany(ProjectVehiclePart::class);
    }

    public function projectOrderProcess(): HasMany
    {
        return $this->hasMany(ProjectOrderProcess::class);
    }

    public function picCountryPhoneCode(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'pic_country_code', 'id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ProjectOrderAttachment::class);
    }

    public function outsourced(): HasMany
    {
        return $this->hasMany(ProjectOrderOutsourced::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(ProjectOrderRequirement::class);
    }

    public function usedRequirements(): HasMany
    {
        return $this->hasMany(ProjectOrderRequirementUsed::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ProjectOrderReport::class);
    }

    /**
     * Relationship with the sales order (warranty_sale_order_eco_id)
     */
    public function warrantySaleOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrderECO::class, 'warranty_sale_order_eco_id');
    }

    /**
     * Relationship with the project contract (warranty_contract_id)
     */
    public function warrantyContract(): BelongsTo
    {
        return $this->belongsTo(ProjectContract::class, 'warranty_contract_id');
    }
}
