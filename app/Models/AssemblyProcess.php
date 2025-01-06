<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssemblyProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'assembly_id',
        'process_id'
    ];

    public function process() {
        return $this->belongsTo(Process::class);
    }
}
