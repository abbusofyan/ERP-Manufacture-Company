<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefrigerationSaleSpecification extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function assembly() {
        return $this->belongsTo(Assembly::class);
    }

    public function items() {
        return $this->hasMany(RefrigerationSaleSpecificationItem::class, 'rs_spec_id');
    }

    public function processes() {
        return $this->hasMany(RefrigerationSaleSpecificationProcess::class, 'specification_id');
    }

    public function detail_spec() {
        return $this->belongsTo(VehicleSpec::class, 'vehicle_spec_id');
    }
}
