<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductHistory extends Model
{
    use HasFactory;

    public $guarded = [];

    const ACTIONS = [
        'products.update' => 'Update',
        'products.store' => 'Create',
        'uploadPartBook' => 'Upload Part Book',
        'uploadProductPhoto' => 'Upload Product Photo',
        'products.updateStatus' => 'Update'
    ];

    protected $appends = [
        'action_text',
    ];

    protected $casts = [
        'data' => 'object',
    ];

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getActionTextAttribute(): string
    {
        return self::ACTIONS[$this->action];
    }
}
