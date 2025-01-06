<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'block',
        'street_name',
        'unit_level',
        'unit_number',
        'city',
        'region',
        'country',
        'postal_code',
        'phone',
        'status',
        'is_default'
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
