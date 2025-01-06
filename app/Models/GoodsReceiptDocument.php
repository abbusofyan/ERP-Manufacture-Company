<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceiptDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'goods_receipt_id',
        'file_name',
        'file_url'
    ];

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }
}
