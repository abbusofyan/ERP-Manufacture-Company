<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngineeringOrder extends Model
{
    use HasFactory;

    const STATUS_PENDING = 1;
    const STATUS_STARTED = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELED = 4;

    const STATUS_TEXT_ARRAY = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_STARTED => 'Started',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELED => 'Canceled',
    ];

    const STATUS_CLASS_ARRAY = [
        self::STATUS_PENDING => 'orange',
        self::STATUS_STARTED => 'green',
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

    protected $guarded = [];

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrderECO::class, 'sales_order_id');
    }

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(RefrigerationSale::class, 'quotation_id');
    }

    public function processes() {
        return $this->hasMany(EngineeringOrderProcess::class);
    }

    public function attachments() {
        return $this->hasMany(EngineeringOrderAttachment::class);
    }

    public function itemMovements()
    {
        return $this->morphMany(ItemMovement::class, 'transaction');
    }
}
