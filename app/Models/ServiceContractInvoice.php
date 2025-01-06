<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceContractInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_contract_id',
        'index',
        'start_date',
        'end_date',
        'status',
        'tax_amount',
        'discount_amount',
        'subtotal_amount',
        'total_amount',
        'footer',
        'file_url',
    ];

    /**
     * Get the service contract associated with the invoice.
     */
    public function serviceContract()
    {
        return $this->belongsTo(ServiceContract::class);
    }
}
