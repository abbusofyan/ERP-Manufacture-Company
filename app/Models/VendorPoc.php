<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPoc extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'first_name',
        'last_name',
        'country_phone_code_id',
        'phone',
        'email',
        'title',
        'status',
        'is_default',
    ];

    const STATUS_NONE = 0;
    const STATUS_APPROVE = 1;
    const STATUS_RESIGN = 2;

    public function countryPhoneCode(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_phone_code_id', 'id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'vendor_id', 'id');
    }
}
