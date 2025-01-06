<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MilestoneItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'milestone_id',
        'stage',
        'calculated_due_date',
        'status',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    protected $appends = [
        'status_text', 'status_class',
    ];

    const STATUS_PROGRESS = 1;
    const STATUS_PENDING = 2;
    const STATUS_COMPLETED = 3;

    const STAGE_STATUS_TEXT = [
        self::STATUS_PROGRESS => 'In Progress',
        self::STATUS_PENDING => 'Pending',
        self::STATUS_COMPLETED => 'Completed',
    ];


    const STAGE_STATUS_CLASS = [
        self::STATUS_PROGRESS => 'green',
        self::STATUS_PENDING => 'gray',
        self::STATUS_COMPLETED => 'primary',
    ];

    public function getStatusTextAttribute(): string
    {
        return $this->status ? self::STAGE_STATUS_TEXT[$this->status] : '';
    }

    public function getStatusClassAttribute(): string
    {
        return $this->status ? self::STAGE_STATUS_CLASS[$this->status] : '';
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class, 'milestone_id');
    }
}
