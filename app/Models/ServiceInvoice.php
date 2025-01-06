<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'service_quotation_id',
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
        'total_paid_amount',
        'remaining_amount',
    ];

    const TYPE_PROFORMA_INVOICE = 1;
    const TYPE_INVOICE = 2;

    const STATUS_INVOICED = 'Invoiced';
    const STATUS_NOT_INVOICED = 'Not Invoiced';
    const STATUS_PAID = 'Paid';
    const STATUS_PARTIAL_PAID = 'Partial Paid';
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
                'code' => 'SVI' . $year . $month . $day . str_pad($service_invoice->id, 3, '0', STR_PAD_LEFT)
            ]);
        });
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS_CLASS[$this->status];
    }

    public function getTotalPaidAmountAttribute(): float
    {
        return $this->paids()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        $totalPaid = $this->getTotalPaidAmountAttribute();
        $total = $this->serviceQuotation ? $this->serviceQuotation->total : 0;
        return round(max($total - $totalPaid, 0), 2);
    }

    // Relationships
    public function serviceQuotation(): BelongsTo
    {
        return $this->belongsTo(ServiceQuotation::class, 'service_quotation_id');
    }

    public function paids(): HasMany
    {
        return $this->hasMany(ServiceInvoicePaid::class);
    }
}
