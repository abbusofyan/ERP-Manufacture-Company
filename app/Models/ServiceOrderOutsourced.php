<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrderOutsourced extends Model
{
    use HasFactory;

    protected $table = 'service_order_outsourced';

    protected $fillable = [
        'service_order_id',
        'name',
        'quantity',
        'price',
        'file_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class, 'service_order_id');
    }
}
