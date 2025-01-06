<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectOrderReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_order_id',
        'name',
        'file_url',
        'remarks',
    ];

    public function projectOrder(): BelongsTo
    {
        return $this->belongsTo(ProjectOrder::class);
    }
}
