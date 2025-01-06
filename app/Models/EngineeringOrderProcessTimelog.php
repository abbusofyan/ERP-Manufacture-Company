<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngineeringOrderProcessTimelog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function engineeringOrderProcess(): BelongsTo
    {
        return $this->belongsTo(EngineeringOrderProcess::class);
    }
}
