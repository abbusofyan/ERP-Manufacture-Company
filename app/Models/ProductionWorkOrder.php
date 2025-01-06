<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionWorkOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'sales_order_id',
        'vehicle_class_wo',
        'vehicle_voltage_wo',
        'unit_model',
        'box_dimension_mm_l',
        'box_dimension_mm_w',
        'box_dimension_mm_h',
        'pe_archie',
        'pe_prema',
        'addition_accessories',
        'receipt_date',
        'received_date',
        'bracket',
        'production_schedule',
        'ibox',
        'final_assy',
        'testing',
        'vehicle_no',
        'log_card',
        'dummy_box',
        'final_delivery',
        'remark',
    ];

    protected $casts = [
        'final_assy' => 'boolean',
        'testing' => 'boolean',
        'log_card' => 'boolean',
    ];

    public function salesOrderECO(): BelongsTo
    {
        return $this->belongsTo(SalesOrderECO::class, 'sales_order_id');
    }
}
