<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'status',
    ];

    protected $appends = [
        'status_text', 'status_button'
    ];

    const IS_ACTIVE_YES = 1;
    const IS_ACTIVE_NO = 0;

    const STATUS_TEXT_ARRAY = [
        self::IS_ACTIVE_YES => 'Active',
        self::IS_ACTIVE_NO => 'Inactive',
    ];

    const STATUS_BUTTON_ARRAY = [
        self::IS_ACTIVE_YES => 'status-success',
        self::IS_ACTIVE_NO => 'status-danger',
    ];

    const DEFAULT_STORE = 'HWEEJAN SINGAPORE';
    const STORAGE_WAREHOUSE = 'TEMPORARY LOCATION';

    public function getStatusTextAttribute(): string
    {
        return self::STATUS_TEXT_ARRAY[$this->status];
    }

    public function getStatusButtonAttribute(): string
    {
        return self::STATUS_BUTTON_ARRAY[$this->status];
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'store_products', 'store_id', 'product_id');
    }

    public function stock() {
        return $this->hasMany(StoreProduct::class);
    }

}
