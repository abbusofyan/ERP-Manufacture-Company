<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectOrderOutsourced extends Model
{
    use HasFactory;

    protected $table = 'project_order_outsourced';

    protected $fillable = [
        'project_order_id',
        'name',
        'quantity',
        'price',
        'file_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function projectOrder(): BelongsTo
    {
        return $this->belongsTo(ProjectOrder::class, 'project_order_id');
    }
}
