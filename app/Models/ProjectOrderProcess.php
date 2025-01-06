<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectOrderProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage',
        'project_order_id',
        'begin_datetime',
        'name',
        'standard_time',
        'total_time',
        'manpower',
        'overtime',
        'efficiency',
        'status',
    ];

    protected $appends = [
        'status_text', 'status_class',
    ];

    const STATUS_PENDING = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_PAUSE = 3;
    const STATUS_COMPLETE = 4;

    const STATUS = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_PAUSE => 'Pause',
        self::STATUS_COMPLETE => 'Complete',
        self::STAGE_COLLECTED => 'Vehicle Collected',
    ];

    const STATUS_CLASS = [
        self::STATUS_PENDING => 'gray',
        self::STATUS_IN_PROGRESS => 'primary',
        self::STATUS_PAUSE => 'orange',
        self::STATUS_COMPLETE => 'green',
        self::STAGE_COLLECTED => 'primary',
    ];

    const STAGE_FIRST_CHECK = 1;
    const STAGE_REPAIR = 2;
    const STAGE_LAST_CHECK = 3;
    const STAGE_PENDING_COLLECTION = 4;
    const STAGE_COLLECTED = 5;

    const STAGES = [
        self::STAGE_FIRST_CHECK => [
            'title' => 'First Check',
            'icon' => '<svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 3.5V7.5C14 8.05228 14.4477 8.5 15 8.5H19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 21.5H7C5.89543 21.5 5 20.6046 5 19.5V5.5C5 4.39543 5.89543 3.5 7 3.5H14L19 8.5V13" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="16.5" cy="18" r="2.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M18.5 20L21 22.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>',
        ],
        self::STAGE_REPAIR => [
            'title' => 'Repair',
            'icon' => '<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5.25002 8.50002H8.25002V5.50002L4.75002 2.00002C7.04447 0.904207 9.78049 1.37363 11.5784 3.17159C13.3764 4.96955 13.8458 7.70557 12.75 10L18.75 16C19.5784 16.8284 19.5784 18.1716 18.75 19C17.9216 19.8284 16.5784 19.8284 15.75 19L9.75002 13C7.45557 14.0958 4.71955 13.6264 2.92159 11.8284C1.12363 10.0305 0.654207 7.29447 1.75002 5.00002L5.25002 8.50002" stroke="#4B465C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M5.25002 8.50002H8.25002V5.50002L4.75002 2.00002C7.04447 0.904207 9.78049 1.37363 11.5784 3.17159C13.3764 4.96955 13.8458 7.70557 12.75 10L18.75 16C19.5784 16.8284 19.5784 18.1716 18.75 19C17.9216 19.8284 16.5784 19.8284 15.75 19L9.75002 13C7.45557 14.0958 4.71955 13.6264 2.92159 11.8284C1.12363 10.0305 0.654207 7.29447 1.75002 5.00002L5.25002 8.50002" stroke="white" stroke-opacity="0.2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>',
        ],
        self::STAGE_LAST_CHECK => [
            'title' => 'Last Check',
            'icon' => '<svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.5 5.5H7.5C6.39543 5.5 5.5 6.39543 5.5 7.5V19.5C5.5 20.6046 6.39543 21.5 7.5 21.5H17.5C18.6046 21.5 19.5 20.6046 19.5 19.5V7.5C19.5 6.39543 18.6046 5.5 17.5 5.5H15.5" stroke="#4B465C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9.5 5.5H7.5C6.39543 5.5 5.5 6.39543 5.5 7.5V19.5C5.5 20.6046 6.39543 21.5 7.5 21.5H17.5C18.6046 21.5 19.5 20.6046 19.5 19.5V7.5C19.5 6.39543 18.6046 5.5 17.5 5.5H15.5" stroke="white" stroke-opacity="0.2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <rect x="9.5" y="3.5" width="6" height="4" rx="2" stroke="#4B465C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></rect>
                        <rect x="9.5" y="3.5" width="6" height="4" rx="2" stroke="white" stroke-opacity="0.2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></rect>
                        <path d="M9.49988 14.5H9.50988" stroke="#4B465C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9.49988 14.5H9.50988" stroke="white" stroke-opacity="0.2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9.49988 17.5H9.50988" stroke="#4B465C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9.49988 17.5H9.50988" stroke="white" stroke-opacity="0.2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M12.5 16.5L13.5 17.5L16.5 14.5" stroke="#4B465C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M12.5 16.5L13.5 17.5L16.5 14.5" stroke="white" stroke-opacity="0.2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>',
        ],
        self::STAGE_PENDING_COLLECTION => [
            'title' => 'Pending Collection',
            'icon' => '<svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.75 11.5L12.75 14.5L20.75 6.5" stroke="#4B465C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9.75 11.5L12.75 14.5L20.75 6.5" stroke="white" stroke-opacity="0.2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M20.75 12.5V18.5C20.75 19.6046 19.8546 20.5 18.75 20.5H6.75C5.64543 20.5 4.75 19.6046 4.75 18.5V6.5C4.75 5.39543 5.64543 4.5 6.75 4.5H15.75" stroke="#4B465C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M20.75 12.5V18.5C20.75 19.6046 19.8546 20.5 18.75 20.5H6.75C5.64543 20.5 4.75 19.6046 4.75 18.5V6.5C4.75 5.39543 5.64543 4.5 6.75 4.5H15.75" stroke="white" stroke-opacity="0.2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>',
        ],
    ];

    public function getStatusTextAttribute(): string
    {
        return self::STATUS[$this->status];
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS_CLASS[$this->status];
    }

    public function projectOrder(): BelongsTo
    {
        return $this->belongsTo(ProjectOrder::class);
    }

    public function timelogs(): HasMany
    {
        return $this->hasMany(ProjectOrderProcessTimelog::class);
    }
}
