<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_id',
        'product_id',
        'quantity',
    ];

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(StorageItem::class, 'storage_id');
    }

    public function storageItem(): BelongsTo
    {
        return $this->belongsTo(StorageItem::class, 'storage_item_id');
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
