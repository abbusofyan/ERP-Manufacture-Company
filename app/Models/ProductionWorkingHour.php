<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductionWorkingHour extends Model
{
    protected $fillable = [
        'start_time',
        'end_time',
        'specific_date',
        'is_default',
        'is_lunch_time',
        'is_dinner_time',
    ];

    /**
     * Scope to filter by specific date or default.
     *
     * @param Builder $query
     * @param string $date
     * @return Builder
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('specific_date', $date)->orWhereNull('specific_date');
    }

    /**
     * Scope to get default working hours.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true)->whereNull('specific_date');
    }

    /**
     * Scope to get lunch time.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeLunchTime($query)
    {
        return $query->where('is_lunch_time', true)->whereNull('specific_date');
    }

    /**
     * Scope to get dinner time.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeDinnerTime($query)
    {
        return $query->where('is_dinner_time', true)->whereNull('specific_date');
    }
}
