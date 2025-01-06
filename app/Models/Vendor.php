<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'country_id',
        'name',
        'country_phone_code_id',
        'phone',
        'uen',
        'account_manager',
        'remark',
        'credit_term',
        'credit_limit',
        'currency',
        'amount_payable',
    ];

    protected $appends = [
        'account_manager_text',
        'credit_term_text',
    ];

    public function getAccountManagerTextAttribute()
    {
        return !empty($this->account_manager) ? $this->getById($this->account_manager, Customer::MANAGER_ARRAY) : null;
    }

    public function getCreditTermTextAttribute()
    {
        return !empty($this->credit_term) ? $this->getById($this->credit_term, Customer::CREDIT_TERM_ARRAY) : null;
    }

    private function getById($managerId, $array)
    {
        $manager = collect($array)->firstWhere('id', $managerId);

        return $manager['text'] ?? null;
    }


    /** Relationship **/
    public function countryPhoneCode(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_phone_code_id', 'id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(VendorAddress::class);
    }

    public function pocs(): HasMany
    {
        return $this->hasMany(Poc::class);
    }
    public function attachments()
    {
        return $this->hasMany(VendorAttachment::class);
    }
    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'vendor_id');
    }
}
