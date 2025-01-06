<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionOrderProcessTimelog extends Model
{
    use HasFactory;

    protected $table = 'production_order_process_timelog';

    protected $fillable = [
        'user_id',
        'production_order_process_id',
        'start_time',
        'end_time',
        'type',
    ];

    public function productionOrderProcess(): BelongsTo
    {
        return $this->belongsTo(ProductionOrderProcess::class, 'production_order_process_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
