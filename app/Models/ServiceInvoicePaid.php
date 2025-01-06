<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceInvoicePaid extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_invoice_id',
        'amount',
        'mode_of_payment',
    ];

    // Relationships
    public function serviceInvoice()
    {
        return $this->belongsTo(ServiceInvoice::class, 'service_invoice_id');
    }
}
