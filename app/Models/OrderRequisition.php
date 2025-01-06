<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRequisition extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function requisition() {
        return $this->belongsTo(Requisition::class);
    }

    public function requisitionItem() {
        return $this->belongsTo(RequisitionItem::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function orderItem() {
        return $this->belongsTo(OrderItem::class);
    }
}
