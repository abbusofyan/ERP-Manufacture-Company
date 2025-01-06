<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'post_type',
        'code',
        'customer_id',
        'shipment',
        'sub_total',
        'discount',
        'gst',
        'total',
        'remarks',
        'status',
        'photo',
    ];

    /** Shipment type **/
    const SHIPMENT_LOCAL = 1;
    const SHIPMENT_EXPORT = 2;

    const SHIPMENT_TEXT = [
        self::SHIPMENT_LOCAL => 'Local',
        self::SHIPMENT_EXPORT => 'Export',
    ];

    const TYPE_ARR = [
        "Type A" => "Type A",
        "Type B" => "Type B",
        "Type C" => "Type C",
        "Type E" => "Type E",
        "Type F" => "Type F",
        "Type G" => "Type G",
        "Type H" => "Type H",
        "Type I" => "Type I",
    ];

    /** Status **/
    const STATUS_DRAFT = 1;
    const STATUS_PENDING = 2;
    const STATUS_REJECT = 3;
    const STATUS_VOID = 4;

    const STATUS = [
        self::STATUS_DRAFT => 'Draft Quote',
        self::STATUS_PENDING => 'Pending Quote Approve',
        self::STATUS_REJECT => 'Rejected Quote',
        self::STATUS_VOID => 'Void Quote',
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
    ];

    const STATUS_CLASS = [
        self::STATUS_DRAFT => 'gray',
        self::STATUS_PENDING => 'orange',
        self::STATUS_REJECT => 'red',
        self::STATUS_VOID => 'green',
    ];

    protected $appends = [
        'status_text', 'status_class', 'shipment_text'
    ];

    public function getStatusTextAttribute(): string
    {
        return self::STATUS[$this->status];
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS_CLASS[$this->status];
    }

    public function getShipmentTextAttribute()
    {
        return self::SHIPMENT_TEXT[$this->shipment];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }
}
