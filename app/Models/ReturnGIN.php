<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnGIN extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'gni_id',
        'gni_item_id',
        'return_qty',
        'serial',
        'reason',
    ];

    /**
     * Relationship to the GIN model
     */
    public function gin()
    {
        return $this->belongsTo(GIN::class, 'gni_id');
    }

    /**
     * Relationship to the GINItem model
     */
    public function ginItem()
    {
        return $this->belongsTo(GINItem::class, 'gni_item_id');
    }
}
