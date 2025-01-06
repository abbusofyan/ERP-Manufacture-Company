<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleSpecItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function material()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Define the relationship with the Assembly model
    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_id');
    }

    public function header() {
        return $this->belongsTo(VehicleSpecHeader::class, 'header_id');
    }
}
