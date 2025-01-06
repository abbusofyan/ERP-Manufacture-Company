<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectOrderProcessTimelog extends Model
{
    use HasFactory;

    protected $table = 'project_order_process_timelog';

    protected $fillable = [
        'project_order_process_id',
        'start_time',
        'end_time',
        'type',
    ];

    public function projectOrderProcess(): BelongsTo
    {
        return $this->belongsTo(ProjectOrderProcess::class, 'project_order_process_id');
    }
}
