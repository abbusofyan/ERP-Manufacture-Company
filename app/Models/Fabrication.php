<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fabrication extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function generateCode() {
        $lastData = Fabrication::orderBy('id', 'DESC')->limit(1)->first();
        if (!$lastData) {
            return '100000001';
        }
        $lastCode = $lastData->code;
        return $lastCode + 1;
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function assembly() {
        return $this->belongsTo(Assembly::class);
    }

}
