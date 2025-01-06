<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorageItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'storage_id',
        'product_id',
        'vendor_id',
        'store_id',
        'location_id',
        'quantity',
        'cost_price',
        'status',
    ];

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
