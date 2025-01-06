<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'project_quotation_id',
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

    const STATUS_CLASS = [
        self::STATUS_INVOICED => 'orange',
        self::STATUS_NOT_INVOICED => 'gray',
        self::STATUS_PAID => 'green',
        self::STATUS_PARTIAL_PAID => 'primary',
    ];

    protected static function booted()
    {
        static::created(function ($project_invoice) {
            $year = date('y');
            $month = date('m');
            $day = date('d');
            $project_invoice->update([
                'code' => 'SVI' . $year . $month . $day . str_pad($project_invoice->id, 3, '0', STR_PAD_LEFT)
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
        $total = $this->projectQuotation ? $this->projectQuotation->total : 0;
        return round(max($total - $totalPaid, 0), 2);
    }

    public function projectQuotation(): BelongsTo
    {
        return $this->belongsTo(ProjectQuotation::class, 'project_quotation_id');
    }

    public function paids(): HasMany
    {
        return $this->hasMany(ProjectInvoicePaid::class);
    }
}
