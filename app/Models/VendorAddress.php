<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class VendorAddress extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'address',
        'unit_no',
        'building_name',
        'postal_code',
        'phone',
        'status',
        'is_default',
    ];

    const STATUS_NONE = 1;
    const STATUS_APPROVE = 2;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'vendor_id', 'id');
    }
}
