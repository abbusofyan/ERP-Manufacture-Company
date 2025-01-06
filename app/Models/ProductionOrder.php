<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'sales_order_id',
        'code',
        'category',
        'start_date',
        'completion_date',
        'status',
        'delivery_date' //-> This attribute is not in the database.,
    ];

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


    public static function generateCode()
    {
        $lastData = ProductionOrder::orderBy('id', 'DESC')->limit(1)->first();
        if (!$lastData) {
            return '100000001';
        }
        $lastCode = $lastData->code;
        return $lastCode + 1;
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrderECO::class, 'sales_order_id');
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(RefrigerationSale::class, 'quotation_id');
    }

    public function materials()
    {
        return $this->hasMany(ProductionOrderMaterial::class);
    }

    public function processes()
    {
        return $this->hasMany(ProductionOrderProcess::class);
    }

    public function attachments()
    {
        return $this->hasMany(ProductionOrderAttachment::class);
    }

    public function processTimelogs()
    {
        return $this->hasManyThrough(
            ProductionOrderProcessTimelog::class,
            ProductionOrderProcess::class,
            'production_order_id', // Foreign key on production_order_process table
            'production_order_process_id', // Foreign key on production_order_process_logs table
            'id', // Local key on production_order table
            'id'  // Local key on production_order_process table
        );
    }

    public function requisitions()
    {
        return $this->morphMany(Requisition::class, 'requisitionable');
    }
}
