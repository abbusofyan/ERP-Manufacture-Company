<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrderMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'product_id',
        'planned_qty',
        'issued_qty',
        'returned_qty',
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
