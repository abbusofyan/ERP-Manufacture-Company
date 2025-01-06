<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngineeringOrderProcess extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function process() {
        return $this->belongsTo(Process::class);
    }

    public function assembly() {
        return $this->belongsTo(Assembly::class);
    }

    public function timelogs()
    {
        return $this->hasMany(EngineeringOrderProcessTimelog::class);
    }
}
