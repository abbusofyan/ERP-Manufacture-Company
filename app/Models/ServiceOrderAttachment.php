<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrderAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_order_id',
        'stage',
        'name',
        'file_url',
        'remarks',
    ];

    protected $appends = ['full_url'];

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function getFullUrlAttribute()
    {
        return $this->file_url ? url('storage/' . $this->file_url) : null;
    }
}
