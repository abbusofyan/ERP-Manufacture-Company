<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Requisitionable extends Model
{
    use HasFactory;

    public $incrementing = false; // No auto-incrementing key
    protected $primaryKey = null; // No primary key

    protected $guarded = [];

    public $timestamps = false;

    public function requisitionable()
    {
        return $this->morphTo();
    }
}
