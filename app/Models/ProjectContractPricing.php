<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectContractPricing extends Model
{
    use HasFactory;

    protected $table = 'project_contract_pricing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_contract_id',
        'type',
        'product_id',
        'assembly_id',
        'price',
        'name',
        'quantity',
        'tax_code',
        'tax_amount',
        'discount',
        'discount_amount',
        'subtotal_amount',
        'total_amount',
    ];

    /**
     * Get the project contract that owns the pricing.
     */
    public function projectContract()
    {
        return $this->belongsTo(ProjectContract::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function assembly(): BelongsTo
    {
        return $this->belongsTo(Assembly::class, 'assembly_id');
    }

    public function vehicleItemDetails(): HasMany
    {
        return $this->hasMany(ProjectContractVehicleItemDetail::class, 'project_contract_pricing_id');
    }
}
