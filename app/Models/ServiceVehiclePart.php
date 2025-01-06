<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceVehiclePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_order_id',
        'storage_item_id',
        'name',
        'quantity',
    ];

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function storageItem()
    {
        return $this->belongsTo(StorageItem::class);
    }
}
