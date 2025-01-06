<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'origin_store_id',
        'destination_store_id',
        'remarks',
        'author',
        'status'
    ];

    const STATUS_PENDING = 1;
    const STATUS_CANCELED = 2;
    const STATUS_CONFIRMED = 3;
    const STATUS_DRAFT = 4;
    const STATUS_COMPLETED = 5;

    const STATUS_TEXT_ARRAY = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CANCELED => 'Canceled',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_COMPLETED => 'Completed',
    ];

    const STATUS_CLASS_ARRAY = [
        self::STATUS_PENDING => 'orange',
        self::STATUS_CONFIRMED => 'orange',
        self::STATUS_CANCELED => 'danger',
        self::STATUS_DRAFT => 'gray',
        self::STATUS_COMPLETED => 'green',
    ];

    protected $appends = [
        'status_text',
        'status_class',
    ];

    public function getStatusTextAttribute(): string
    {
        return self::STATUS_TEXT_ARRAY[$this->status];
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS_CLASS_ARRAY[$this->status];
    }

    public function originStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'origin_store_id');
    }

    public function destinationStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'destination_store_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author');
    }

    public function transferItems(): HasMany
    {
        return $this->hasMany(TransferItem::class);
    }

    public function itemMovements()
    {
        return $this->morphMany(ItemMovement::class, 'transaction');
    }
}
