<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RefrigerationSaleInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'refrigeration_sale_id',
        'type',
        'name',
        'file_url',
        'delivery_order_invoice_url',
        'status',
        'tax_amount',
        'discount_amount',
        'subtotal_amount',
        'total_amount',
        'finance_amount',
        'deposit_required',
        'rounding',
        'freight_charges',
        'customer_reference_number',
        'attachment',
        'amend_customer_address',
        'footer'
    ];

    protected $appends = [
        'status_class',
    ];

    const TYPE_PROFORMA_INVOICE = 1;
    const TYPE_INVOICE = 2;

    const STATUS_INVOICED = 1;
    const STATUS_NOT_INVOICED = 2;
    const STATUS_PAID = 3;
    const STATUS_PARTIAL_PAID = 4;
    const STATUSES = [
        self::STATUS_INVOICED => 'Invoiced',
        self::STATUS_NOT_INVOICED => 'Not Invoiced',
        self::STATUS_PAID => 'Paid',
        self::STATUS_PARTIAL_PAID => 'Partial Paid',
    ];

    const STATUS_CLASS = [
        self::STATUS_INVOICED => 'orange',
        self::STATUS_NOT_INVOICED => 'gray',
        self::STATUS_PAID => 'green',
        self::STATUS_PARTIAL_PAID => 'primary',
    ];

    protected static function booted()
    {
        static::created(function ($service_invoice) {
            // Get the current date parts
            $year = date('y');  // Last two digits of the year
            $month = date('m'); // Two-digit month
            $day = date('d');   // Two-digit day

            // Format the code as SVI + year + month + day + padded id
            $service_invoice->update([
                'code' => 'QRF-I' . $year . $month . $day . str_pad($service_invoice->id, 3, '0', STR_PAD_LEFT)
            ]);
        });
    }

    public function getStatusClassAttribute(): string
    {
        if (!$this->status) return '';
        return self::STATUS_CLASS[$this->status];
    }

    // Relationships
    public function refrigerationSale(): BelongsTo
    {
        return $this->belongsTo(RefrigerationSale::class, 'refrigeration_sale_id');
    }
}
