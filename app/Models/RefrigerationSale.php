<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefrigerationSale extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'customer_id',
        'confirmed_by',
        'name',
        'email',
        'phone_number',
        'shipment',
        'photo',
        'sub_total',
        'discount',
        'gst',
        'total',
        'note',
        'footer',
        'post_type',
        'status',
        'deposit_value',
        'deposit_option',
        'finance_amount',
        'payment_method',
        'terms',
        'validity_date',
        'estimated_delivery_date',
        'currency',
        'currency_rate',
        'rounding',
        'mockup_image_url',
    ];

    /** Shipment type **/
    const SHIPMENT_LOCAL = 1;
    const SHIPMENT_EXPORT = 2;

    const SHIPMENT_TEXT = [
        self::SHIPMENT_LOCAL => 'Local',
        self::SHIPMENT_EXPORT => 'Export',
    ];

    const SHIPMENT_URL = [
        self::SHIPMENT_LOCAL => 'local',
        self::SHIPMENT_EXPORT => 'export',
    ];

    const SHIPMENT_PARAM = [
        'local' => self::SHIPMENT_LOCAL,
        'overseas' => self::SHIPMENT_EXPORT,
    ];

    /** Post type **/
    const POST_TYPE_RS = 1;
    const POST_TYPE_PART = 2;
    const POST_TYPE_HYGIENE = 3;

    /** Status **/
    const STATUS_DRAFT = 1;
    const STATUS_PENDING = 2;
    const STATUS_REJECT = 3;
    const STATUS_VOID = 4;
    const STATUS_CONFIRMED = 5;
    const STATUS_INVOICED = 6;

    const STATUS = [
        self::STATUS_DRAFT => 'Draft Quote',
        self::STATUS_PENDING => 'Pending Quote Approve',
        self::STATUS_REJECT => 'Rejected Quote',
        self::STATUS_VOID => 'Void Quote',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_INVOICED => 'Invoiced',
    ];

    const STATUS_SELECT = [
        [
            'id' => self::STATUS_DRAFT,
            'text' => self::STATUS[self::STATUS_DRAFT],
        ],
        [
            'id' => self::STATUS_PENDING,
            'text' => self::STATUS[self::STATUS_PENDING],
        ],
        [
            'id' => self::STATUS_REJECT,
            'text' => self::STATUS[self::STATUS_REJECT],
        ],
        [
            'id' => self::STATUS_VOID,
            'text' => self::STATUS[self::STATUS_VOID],
        ],
        [
            'id' => self::STATUS_CONFIRMED,
            'text' => self::STATUS[self::STATUS_CONFIRMED],
        ],
        [
            'id' => self::STATUS_INVOICED,
            'text' => self::STATUS[self::STATUS_INVOICED],
        ],
    ];

    const STATUS_CLASS = [
        self::STATUS_DRAFT => 'gray',
        self::STATUS_PENDING => 'orange',
        self::STATUS_REJECT => 'danger',
        self::STATUS_VOID => 'danger',
        self::STATUS_CONFIRMED => 'green',
        self::STATUS_INVOICED => 'green',
    ];

    protected $appends = [
        'status_text', 'status_class', 'shipment_text', 'shipment_url'
    ];

    public function getStatusTextAttribute(): string
    {
        return $this->status ? self::STATUS[$this->status] : '';
    }

    public function getStatusClassAttribute(): string
    {
        return $this->status ? self::STATUS_CLASS[$this->status] : '';
    }

    public function getShipmentTextAttribute()
    {
        return self::SHIPMENT_TEXT[$this->shipment];
    }

    public function getShipmentUrlAttribute()
    {
        return self::SHIPMENT_URL[$this->shipment];
    }

    public function headers(): HasMany
    {
        return $this->hasMany(RefrigerationSaleHeader::class, 'rs_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function assembly(): BelongsTo
    {
        return $this->belongsTo(Assembly::class, 'assembly_id');
    }

    public function specs(): HasMany
    {
        return $this->hasMany(RefrigerationSaleSpecification::class, 'quotation_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(RefrigerationSaleInvoice::class)
            ->where('type', RefrigerationSaleInvoice::TYPE_INVOICE)
            ->latest();
    }

    public function proformaInvoice(): HasOne
    {
        return $this->hasOne(RefrigerationSaleInvoice::class)
            ->where('type', RefrigerationSaleInvoice::TYPE_PROFORMA_INVOICE)
            ->latest();
    }
}
