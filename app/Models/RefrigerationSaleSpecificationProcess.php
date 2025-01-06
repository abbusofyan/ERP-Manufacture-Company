<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefrigerationSaleSpecificationProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'specification_id',
        'user_id',
        'process_id',
        'started_at',
        'ended_at',
        'overtime',
        'efficiency'
    ];

    public function process() {
        return $this->belongsTo(Process::class);
    }
}
