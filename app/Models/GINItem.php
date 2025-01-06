<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GINItem extends Model
{
    use HasFactory;

    public $table = 'gin_items';

    public $fillable = [
        'gin_id',
        'requisition_id',
        'requisition_item_id',
        'order_qty',
        'issued_qty',
        'returned_qty'
    ];

    public function requisitionItem() {
        return $this->belongsTo(RequisitionItem::class);
    }
}
