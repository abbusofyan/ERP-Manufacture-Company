<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishGoodsTransaction extends Model
{
    use HasFactory;

    public $guarded = [];

    public static function generateTransactionNo()
    {
        $latestData = self::orderBy('id', 'DESC')->first();
        $prefix = 'FGS';
        $length = 6;

        if ($latestData && str_starts_with($latestData->code, $prefix)) {
            $currentNumber = (int) substr($latestData->code, strlen($prefix));
            $newNumber = $currentNumber + 1;
            $paddedNumber = str_pad($newNumber, $length, '0', STR_PAD_LEFT);
            return $prefix . $paddedNumber;
        } else {
            return $prefix . str_pad(1, $length, '0', STR_PAD_LEFT);
        }
    }

    public function finishGoods() {
        return $this->belongsTo(FinishGoods::class);
    }

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }




}
