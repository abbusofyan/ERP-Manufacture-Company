<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'store_id',
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

    const DEFAULT_LOCATION = 'Main Area';
    const DEFAULT_CODE = 'ST';

    public static function defaultLocation($store_id) {
        return self::where('code', 'ST')->where('store_id', $store_id)->first();
    }

    public function getStatusTextAttribute(): string
    {
        return self::STATUS_TEXT_ARRAY[$this->status];
    }

    public function getStatusButtonAttribute(): string
    {
        return self::STATUS_BUTTON_ARRAY[$this->status];
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }
}
