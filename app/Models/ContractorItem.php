<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractorItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'contractor_id',
        'remarks',
        'wip_location',
        'start_date',
        'edd',
        'parking_location',
        'done_date',
    ];

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }
}
