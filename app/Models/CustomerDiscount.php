<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'category_id',
        'category_name',
        'discount_percentage',
        'is_default',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    const STATUS_NONE = 1;
    const STATUS_APPROVE = 2;

    /**
     * Get the customer that owns the discount.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
