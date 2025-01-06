<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function goodsReceipt() {
        return $this->belongsTo(GoodsReceipt::class);
    }
}
