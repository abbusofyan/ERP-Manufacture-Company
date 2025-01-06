<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectContractInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_contract_id',
        'index',
        'start_date',
        'end_date',
        'status',
        'tax_amount',
        'discount_amount',
        'subtotal_amount',
        'total_amount',
        'footer',
        'file_url',
    ];

    public function projectContract(): BelongsTo
    {
        return $this->belongsTo(ProjectContract::class);
    }
}
