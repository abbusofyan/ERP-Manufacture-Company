<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'bank_name',
        'bank_code',
        'branch_code',
        'account_number',
        'swift_code',
        'is_default',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    const STATUS_NONE = 1;
    const STATUS_APPROVE = 2;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
