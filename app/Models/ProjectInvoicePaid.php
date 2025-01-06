<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectInvoicePaid extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_invoice_id',
        'amount',
        'mode_of_payment',
    ];

    public function projectInvoice(): BelongsTo
    {
        return $this->belongsTo(ProjectInvoice::class, 'project_invoice_id');
    }
}
