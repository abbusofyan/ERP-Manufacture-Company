<?php

namespace App\Models;

use App\Observers\StorageObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Storage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'grn_number',
        'product_count',
        'status',
    ];

    const STATUS_COMPLETED = 1;
    const STATUS_DRAFT = 0;
    const STATUS = [
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_DRAFT => 'Draft',
    ];

    protected $appends = [
        'status_text',
        'status_class',
    ];

    protected static function boot()
    {
        parent::boot();
        self::observe(StorageObserver::class);
    }

    public function getStatusTextAttribute(): string
    {
        return self::STATUS[$this->status];
    }

    public function getStatusClassAttribute(): string
    {
        return $this->status != self::STATUS_COMPLETED ? 'el-tag orange' : 'el-tag green';
    }

    public function storageItems()
    {
        return $this->hasMany(StorageItem::class);
    }
}
