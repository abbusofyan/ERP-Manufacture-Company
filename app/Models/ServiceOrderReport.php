<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrderReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_order_id',
        'name',
        'file_url',
        'remarks',
    ];

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }
}
