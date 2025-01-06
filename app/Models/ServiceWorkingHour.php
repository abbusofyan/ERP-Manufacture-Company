<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceWorkingHour extends Model
{
    protected $fillable = [
        'start_time',
        'end_time',
        'specific_date',
        'is_default',
        'is_lunch_time',
        'is_dinner_time',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_lunch_time' => 'boolean',
        'is_dinner_time' => 'boolean',
    ];

    /**
     * Scope to filter by specific date or default.
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('specific_date', $date)->orWhereNull('specific_date');
    }

    /**
     * Scope to get default working hours.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true)->whereNull('specific_date');
    }

    /**
     * Scope to get lunch time.
     */
    public function scopeLunchTime($query)
    {
        return $query->where('is_lunch_time', true)->whereNull('specific_date');
    }

    /**
     * Scope to get dinner time.
     */
    public function scopeDinnerTime($query)
    {
        return $query->where('is_dinner_time', true)->whereNull('specific_date');
    }
}
