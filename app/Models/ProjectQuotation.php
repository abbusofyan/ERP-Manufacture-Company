<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectQuotation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'project_order_id',
        'status',
        'customer_id',
        'vehicle_id',
        'pic_first_name',
        'pic_last_name',
        'pic_title',
        'pic_country_code',
        'pic_phone_number',
        'pic_email',
        'project_order_type',
        'project_order_value',
        'remark',
        'footer_text',
        'deposit_value',
        'deposit_option',
        'finance_amount',
        'payment_method',
        'terms',
        'validity_date',
        'currency',
        'currency_rate',
        'rounding',
        'sub_total',
        'discount',
        'gst_rate',
        'gst_total',
        'total',
        'signed_image_url',
        'image_url',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    protected $appends = [
        'status_text', 'status_class',
    ];

    const PREFIX = 'PJQ';

    const TYPES = [
        ['id' => 'Refrigeration System Repair', 'text' => 'Refrigeration System Repair'],
        ['id' => 'Insulation Box Repair', 'text' => 'Insulation Box Repair'],
        ['id' => 'CMP', 'text' => 'CMP'],
        ['id' => 'CMP (temp for invalid cmp)', 'text' => 'CMP (temp for invalid cmp)'],
    ];

    const DEPOSIT_OPTION = [
        ['id' => 'Refundable (No-tax)', 'text' => 'Refundable (No-tax)'],
        ['id' => 'Non-Refundable (with Tax)', 'text' => 'Non-Refundable (with Tax)'],
    ];

    const PAYMENT_METHOD = [
        ['id' => 'Cheque', 'text' => 'Cheque'],
        ['id' => 'Cash', 'text' => 'Cash'],
        ['id' => 'Bank Transfer', 'text' => 'Bank Transfer'],
    ];

    const CURRENCIES = [
        ['id' => 'SGD', 'text' => 'SGD'],
        ['id' => 'MYR', 'text' => 'MYR'],
    ];

    const STATUS_PENDING = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_VOID = 3;

    const STATUS_INVOICED = 4;

    const STATUS = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ACCEPTED => 'Confirmed',
        self::STATUS_VOID => 'Void',
        self::STATUS_INVOICED => 'Invoiced',
    ];

    const STATUS_SELECT2 = [
        ['id' => self::STATUS_PENDING, 'text' => 'Pending'],
        ['id' => self::STATUS_ACCEPTED, 'text' => 'Accepted'],
        ['id' => self::STATUS_VOID, 'text' => 'Void'],
        ['id' => self::STATUS_INVOICED, 'text' => 'Invoiced'],
    ];

    const STATUS_CLASS = [
        self::STATUS_PENDING => 'orange',
        self::STATUS_ACCEPTED => 'green',
        self::STATUS_VOID => 'danger',
        self::STATUS_INVOICED => 'green',
    ];

    protected static function booted()
    {
        static::created(function ($service_quotation) {
            // Get the current date parts
            $year = date('y');  // Last two digits of the year
            $month = date('m'); // Two-digit month
            $day = date('d');   // Two-digit day

            // Format the code as SVQ + year + month + day + padded id
            $service_quotation->update([
                'code' => self::PREFIX . $year . $month . $day . str_pad($service_quotation->id, 3, '0', STR_PAD_LEFT)
            ]);
        });
    }

    public function getStatusTextAttribute(): string
    {
        return self::STATUS[$this->status];
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS_CLASS[$this->status];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function projectOrder(): BelongsTo
    {
        return $this->belongsTo(ProjectOrder::class, 'project_order_id');
    }

    public function vehicleParts(): HasMany
    {
        return $this->hasMany(ProjectQuotationVehiclePart::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(ProjectInvoice::class)
            ->where('type', ProjectInvoice::TYPE_INVOICE);
    }

    public function proformaInvoice(): HasOne
    {
        return $this->hasOne(ProjectInvoice::class)
            ->where('type', ProjectInvoice::TYPE_PROFORMA_INVOICE)
            ->latest();
    }

    public function updatePricing(): void
    {
        $subTotal = 0;
        $discountTotal = 0;
        $gstTotal = 0;
        $total = 0;
        $rounding = $this->rounding ?? 2;

        foreach ($this->vehicleParts as $part) {
            $quantity = $part->quantity ?? 0;
            $costPrice = $part->storageItem->cost_price ?? 0;
            $discount = $part->discount ?? 0;
            $taxValue = $part->tax_value ?? 0;

            $partSubtotal = $costPrice * $quantity;
            $partDiscountAmount = ($partSubtotal * $discount) / 100;

            $part->update([
                'subtotal_amount' => number_format($partSubtotal, $rounding),
                'discount_amount' => number_format($partDiscountAmount, $rounding),
                'tax_amount' => number_format(($partSubtotal - $partDiscountAmount) * ($taxValue / 100), $rounding),
                'total_amount' => number_format(($partSubtotal - $partDiscountAmount) + (($partSubtotal - $partDiscountAmount) * ($taxValue / 100)), $rounding),
            ]);

            if ($part->is_foc) {
                continue;
            }

            $subTotal += $part->subtotal_amount;
            $discountTotal += $part->discount_amount;
            $gstTotal += $part->tax_amount;
            $total += $part->total_amount;
        }

        $this->update([
            'sub_total' => $subTotal,
            'discount' => $discountTotal,
            'gst_total' => $gstTotal,
            'total' => $total,
        ]);
    }
}
