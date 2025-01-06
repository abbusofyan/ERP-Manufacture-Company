<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assembly extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'category',
        'uom',
        'status'
    ];

    protected $appends = [
        'text',
    ];

    // protected static function boot()
    // {
    //     parent::boot();
    //
    //     static::deleting(function ($Assembly) {
    //         $Assembly->code = $Assembly->code . '-deleted';
    //         $Assembly->save();
    //     });
    //
    //     static::addGlobalScope('active', function($query) {
    //         $query->where('status', 'Active');
    //     });
    // }

    public function getTextAttribute(): ?string
    {
        return $this->code . ' | ' . $this->name;
    }

    public static function generateCode(): string
    {
        $lastCode = Assembly::withoutGlobalScope('active')->latest('id')->value('code');

        $number = $lastCode ? (int) substr($lastCode, 4) : 0;
        $newNumber = $number + 1;

        return 'BOM-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function materials() {
        return $this->hasMany(AssemblyMaterial::class);
    }

    public function processes() {
        return $this->hasMany(AssemblyProcess::class);
    }

    public function product() {
        return $this->hasOne(Product::class, 'sku','code');
    }
}
