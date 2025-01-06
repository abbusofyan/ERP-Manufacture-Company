<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Illuminate\Support\Facades\Route;
use App\Services\ProductHistoryService;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'category_id',
        'sku',
        'name',
        'description',
        'uom_id',
        'reserved_qty',
        'requested_qty',
        'committed_qty',
        'ordered_qty',
        'weight',
        'dimension_l',
        'dimension_w',
        'dimension_h',
        'expense_acc',
        'assembly',
        'case_pack',
        'lead_time',
        'auto_reorder',
        'reorder_level',
        'quantity_to_reorder',
        'stock',
        'remark',
        'selling_price',
        'effective_date_selling_price',
        'landed_cost',
        'status',
        'can_be_assembled',
        'assembly_id',
        'fg_id',
        'created_by',
        'updated_by'
    ];

    const DEFAULT_TYPE = 'Inventory';

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active',
    ];

    const AUTO_REORDER_INACTIVE = 0;
    const AUTO_REORDER_ACTIVE = 1;
    const AUTO_REORDER_STATUS = [
        self::AUTO_REORDER_INACTIVE => 'Inactive',
        self::AUTO_REORDER_ACTIVE => 'Active',
    ];

    protected $appends = [
        'text',
        'status_text',
        'auto_reorder_text',
        'calculated_stock',
        'vendor1',
        'vendor2',
        'vendor3',
        'quantity_after_committed',
        'actual_stock',
        'available_stock',
        'last_purchase_cost'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($product) {
            $productHistoryService = new ProductHistoryService;
            $productHistoryService->addProductHistory($product);
        });

        static::updated(function ($product) {
            $productHistoryService = new ProductHistoryService;
            $productHistoryService->addProductHistory($product);
        });
    }

    public function getVendor1Attribute()
    {
        return $this->vendor1();
    }

    public function getVendor2Attribute()
    {
        return $this->vendor2();
    }

    public function getVendor3Attribute()
    {
        return $this->vendor3();
    }

    public function getActualStockAttribute()
    {
        return $this->stock;
    }

    public function getAvailableStockAttribute()
    {
        return $this->stock - $this->committed_qty;
    }

    public function getTextAttribute(): ?string
    {
        return $this->sku . ' | ' . $this->name;
    }

    public function getStatusTextAttribute(): string
    {
        return self::STATUS[$this->status];
    }

    public function getAutoReorderTextAttribute(): string
    {
        return self::STATUS[$this->status];
    }

    public function getCalculatedStockAttribute()
    {
        $calculatedStock = $this->stock - $this->committed_qty;
        if ($calculatedStock <= 0) {
            return 0;
        }
        return $calculatedStock;
    }

    /**
     * Get the quantity after committed.
     *
     * @return int
     */
    public function getQuantityAfterCommittedAttribute(): int
    {
        return max(0, $this->stock - $this->committed_qty);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function uom()
    {
        return $this->belongsTo(UnitOfMeasurement::class, 'uom_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'product_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProductDocument::class, 'product_id');
    }

    public function storageItems(): HasMany
    {
        return $this->hasMany(StorageItem::class);
    }

    // public function getStockAttribute()
    // {
    //     return $this->storageItems()->where('status', 1)->sum('quantity') ?? 0;
    // }

    public function materials()
    {
        return $this->hasMany(AssemblyMaterial::class);
    }

    public function addReservedQty($qty)
    {
        $this->reserved_qty += $qty;
        $this->save();
        return $this;
    }

    public function subReservedQty($qty)
    {
        $this->reserved_qty -= $qty;
        $this->save();
        return $this;
    }

    /**
     * Add to the current requested_qty.
     *
     * @param int $qty
     * @return void
     */
    public function addRequestedQty($qty)
    {
        $this->requested_qty += $qty;
        $this->save();
        return $this;
    }

    public function subRequestedQty($qty)
    {
        $this->requested_qty -= $qty;
        $this->save();
        return $this;
    }

    /**
     * Add to the current committed_qty.
     *
     * @param int $qty
     * @return void
     */
    public function addCommittedQty($qty)
    {
        $this->committed_qty += $qty;
        $this->save();
        return $this;
    }

    public function subCommittedQty($qty)
    {
        $this->committed_qty -= $qty;
        $this->save();
        return $this;
    }

    /**
     * Add to the current ordered_qty.
     *
     * @param int $qty
     * @return void
     */
    public function addOrderedQty($qty)
    {
        $this->ordered_qty += $qty;
        $this->save();
        return $this;
    }

    public function subOrderedQty($qty)
    {
        $this->ordered_qty -= $qty;
        $this->save();
        return $this;
    }

    /**
     * Add to the current stock.
     *
     * @param int $qty
     * @return void
     */
    public function addStock($qty)
    {
        $this->stock += $qty;
        $this->save();
        return $this;
    }

    public function subStock($qty)
    {
        $this->stock -= $qty;
        $this->save();
        return $this;
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_products', 'product_id', 'store_id')
                    ->using(StoreProduct::class)  // Use the custom pivot model
                    ->withPivot(['stock', 'location_id']);  // Get stock and location_id from the pivot table
    }

    public function photos() {
        return $this->hasMany(ProductPhoto::class)->orderBy('id', 'desc');
    }

    public function storeProduct($storeId)
    {
        return $this->hasOne(StoreProduct::class, 'product_id', 'id')
                    ->where('store_id', $storeId);
    }

    public function salesPhotos()
    {
        return $this->hasMany(ProductPhoto::class)->where('is_sales_photo', true)->orderBy('id', 'DESC');
    }

    // public function assembly()
    // {
    //     // return $this->belongsTo(Assembly::class, 'sku', 'code');
    //     return $this->belongsTo(Assembly::class);
    // }

    // public function finishGoods()
    // {
    //     return $this->belongsTo(FinishGoods::class, 'fg_id');
    // }

    public function lowestCost()
    {
        return $this->orderItems()
            ->where('created_at', '>=', now()->subYears(3))
            ->min('unit_price');
    }

    public function highestCost()
    {
        return $this->orderItems()
            ->where('created_at', '>=', now()->subYears(3))
            ->max('unit_price');
    }

    public function lastCost()
    {
        return $this->orderItems()
            ->where('created_at', '>=', now()->subYears(3))
            ->orderByDesc('created_at')
            ->value('unit_price');
    }

    public function vendor1()
    {
        return $this->getVendorPrice(1);
    }

    public function vendor2()
    {
        return $this->getVendorPrice(2);
    }

    public function vendor3()
    {
        return $this->getVendorPrice(3);
    }

    protected function getVendorPrice($rank)
    {
        $prices = $this->prices()->with('vendor')->where('price', '>', 0)->orderBy('price', 'asc')->take(3)->get();

        if ($prices->count() >= $rank) {
            return $prices[$rank - 1];
        }

        return null;
    }

    public function __get($name)
    {
        if (method_exists($this, $name)) {
            return $this->$name();
        }

        return parent::__get($name);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function assembly() {
        return $this->hasOne(Assembly::class, 'code','sku');
    }

    public function finishGoods() {
        return $this->hasOne(FinishGoods::class, 'code','sku');
    }

    public function requisitions()
    {
        return $this->hasManyThrough(
            Requisition::class,
            RequisitionItem::class,
            'product_id',
            'id',
            'id',
            'requisition_id'
        );
    }

    public static function generateItemId() {
        $date = now()->format('ymd');
        $prefix = "OTH<{$date}>";

        $lastItemId = DB::table('products')
            ->where('sku', 'like', "{$prefix}%")
            ->orderBy('sku', 'desc')
            ->value('sku');

        if ($lastItemId) {
            preg_match('/<(\d+)>$/', $lastItemId, $matches);
            $lastSequence = isset($matches[1]) ? (int)$matches[1] : 0;
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        $sequence = str_pad($newSequence, 3, '0', STR_PAD_LEFT);

        $newItemId = "{$prefix}<{$sequence}>";
        return $newItemId;
    }

    public function requisitionItems()
    {
        return $this->hasMany(RequisitionItem::class);
    }

    public function listRequest() {
        return DB::table('requisition_items as ri')
            ->join('requisitions as r', 'ri.requisition_id', '=', 'r.id')
            ->where('r.status', '!=', Requisition::PENDING_STATUS)
            ->where('ri.product_id', $this->id)
            ->whereRaw('(ri.requested_qty - ri.issued_qty) > 0')
            ->select(
                'ri.requested_qty as qty',
                'r.code',
                'r.id as requisition_id',
                'r.type',
                'r.is_urgent',
                'r.status',
                'r.created_at'
            )->get();
    }

    public function listCommit() {
        return DB::table('requisition_items as ri')
            ->join('requisitions as r', 'ri.requisition_id', '=', 'r.id')
            ->where('r.status', '!=', Requisition::PENDING_STATUS)
            ->where('ri.product_id', $this->id)
            ->whereRaw('ri.committed_qty > 0')
            ->select(
                'ri.committed_qty as qty',
                'r.code',
                'r.type',
                'r.is_urgent',
                'r.status',
                'r.id as requisition_id',
                'r.created_at'
            )->get();
    }

    public function listOrder() {
        return DB::table('orders as o')
            ->join('order_items as oi', 'oi.order_id', '=', 'o.id')
            ->join('vendors as v', 'o.vendor_id', '=', 'v.id')
            ->where('o.status', Order::STATUS_CONFIRMED)
            ->where('oi.product_id', $this->id)
            ->select(
                'oi.unit_price',
                'oi.quantity',
                'oi.total',
                'o.code',
                'o.id as order_id',
                'o.created_at',
                'v.name as vendor_name',
                'v.code as vendor_code',
                'o.status'
            )->get();
    }

    public function scopeByVendorName($query, $vendorName)
    {
        return $query->whereHas('prices.vendor', function ($query) use ($vendorName) {
            $query->where('name', $vendorName);
        });
    }

    public function histories() {
        return $this->hasMany(ProductHistory::class);
    }

    public function getLastPurchaseCostAttribute()
    {
        $orderItem = OrderItem::where('product_id', $this->id)->orderBy('id', 'DESC')->first();
        return $orderItem ? $orderItem->unit_price : 0;
    }

    // public static function getLastNonInventoryCode() {
    //     $date = now()->format('ymd');
    //     $prefix = "OTH<{$date}>";
    //
    //     return Product::where('sku', 'like', "{$prefix}%")
    //         ->orderBy('sku', 'desc')
    //         ->value('sku');
    // }
    //
    // public static function generateItemID() {
    //     $lastCode = self::getLastNonInventoryCode();
    //     if (!$lastCode) {
    //         return self::generateNewItemID();
    //     } else {
    //         return self::generateNextItemID();
    //     }
    // }
    //
    // public static function generateNewItemID() {
    //     $now = new DateTime();
    //
    //     $year = $now->format('y'); // Last two digits of the year
    //     $month = $now->format('m'); // Month in two digits
    //     $day = $now->format('d'); // Day in two digits
    //
    //     $formattedRunningNumber = '000';
    //
    //     return "OTH<{$year}{$month}{$day}><{$formattedRunningNumber}>";
    // }
}
