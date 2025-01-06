<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectVehiclePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_order_id',
        'storage_item_id',
        'name',
        'quantity',
    ];

    public function projectOrder(): BelongsTo
    {
        return $this->belongsTo(ProjectOrder::class);
    }

    public function storageItem(): BelongsTo
    {
        return $this->belongsTo(StorageItem::class);
    }
}
