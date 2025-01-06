<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrderProcessTimelog extends Model
{
    use HasFactory;

    protected $table = 'service_order_process_timelog';

    protected $fillable = ['service_order_process_id', 'start_time', 'end_time', 'type'];

    public function serviceOrderProcess(): BelongsTo
    {
        return $this->belongsTo(ServiceOrderProcess::class, 'service_order_process_id');
    }
}
