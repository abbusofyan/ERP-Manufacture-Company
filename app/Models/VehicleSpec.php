<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleSpec extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function generateCode() {
        $lastData = VehicleSpec::orderBy('id', 'DESC')->limit(1)->first();
        if (!$lastData) {
            return '100000001';
        }
        $lastCode = $lastData->code;
        return $lastCode + 1;
    }


    public function items() {
        return $this->hasMany(VehicleSpecItem::class);
    }
}
