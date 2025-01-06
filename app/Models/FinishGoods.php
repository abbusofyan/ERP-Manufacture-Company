<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishGoods extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active',
    ];

    public $fillable = [
        'code',
        'assembly_id',
        'store_id',
        'category_id',
        'selling_price',
        'convert_qty',
        'created_by'
    ];

    // protected $appends = [
    //     'status_text',
    // ];

    // protected static function boot()
    // {
    //     parent::boot();
    //
    //     static::deleting(function ($finishGoods) {
    //         $finishGoods->code = $finishGoods->code . '-deleted';
    //         $finishGoods->save();
    //     });
    //
    //     static::addGlobalScope('active', function($query) {
    //         $query->where('status', 1);
    //     });
    // }

    public function getTextAttribute(): ?string
    {
        return $this->code . ' | ' . $this->name;
    }

    public function getStatusTextAttribute(): string
    {
        return self::STATUS[$this->status];
    }

    public function assembly() {
        return $this->belongsTo(Assembly::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function items() {
        return $this->hasMany(FinishGoodsItem::class);
    }

    public static function checkExistByCode($code) {
        return self::where('code', $code)->first() ? true : false;
    }

    public function product() {
        return $this->hasOne(Product::class, 'sku','code');
    }

    public function itemMovements()
    {
        return $this->morphMany(ItemMovement::class, 'transaction');
    }

}
