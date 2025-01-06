<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GIN extends Model
{
    use HasFactory;

    protected $table = 'gins';

    protected $fillable = [
        'code',
        'transaction_type',
        'transaction_code',
        'status',
        'remark',
        'issued_by',
        'issued_to',
        'created_by'
    ];

    protected $appends = [
        'status_text',
        'status_class',
    ];

    const PENDING_STATUS = 1;
    const CONFIRMED_STATUS = 2;

    const STATUS_ARRAY = [
        self::PENDING_STATUS => 'Pending',
        self::CONFIRMED_STATUS => 'Confirmed',
    ];

    const STATUS_CLASS_ARRAY = [
        self::PENDING_STATUS => 'orange',
        self::CONFIRMED_STATUS => 'green',
    ];

    public function getStatusTextAttribute(): string
    {
        return self::STATUS_ARRAY[$this->status];
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS_CLASS_ARRAY[$this->status];
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }

    public function requisitionItem()
    {
        return $this->belongsTo(RequisitionItem::class, 'requisition_item_id');
    }

    public function returns()
    {
        return $this->hasMany(ReturnGIN::class, 'gni_id');
    }

    public function issuedTo()
    {
        return $this->belongsTo(User::class, 'issued_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items() {
        return $this->hasMany(GINItem::class, 'gin_id');
    }
}
