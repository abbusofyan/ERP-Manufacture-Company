<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssemblyMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'assembly_id',
        'product_id',
        'qty'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function assembly() {
        return $this->belongsTo(Assembly::class);
    }
}
