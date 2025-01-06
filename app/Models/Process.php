<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'standard_time_hour',
        'standard_time_minute',
        'type',
        'manpower'
    ];

    public function detail() {
        return $this->hasMany(ProcessDetail::class);
    }
}
