<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMovement extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'id',
        'product_id',
        'store_id',
        'movement_type',
        'transaction_type',
        'transaction_id',
        'transaction_code',
        'quantity',
        'remark'
    ];

    const MOVEMENT_TYPE_MINUS = '-';
    const MOVEMENT_TYPE_PLUS = '+';
    const REQUISITION_TYPE = 1;
    const INVENTORY_TRANSFER_TYPE = 2;//
    const PURCHASE_ORDER_TYPE = 3;
    const FINISH_GOODS_TYPE = 4;
    const GIN_TYPE = 5;//
    const OPENING_TYPE = 6;//
    const GOOD_RECEIPT_TYPE = 7;//
    const RETURN_TYPE = 8;//
    const ITEM_ADJ_TYPE = 9;//
    const ITEM_ASSEMBLY_TYPE = 10;//

    const TRANSACTION_TYPE_ARRAY = [
        self::REQUISITION_TYPE => 'Requisition',
        self::INVENTORY_TRANSFER_TYPE => 'Inventory Transfer',
        self::PURCHASE_ORDER_TYPE => 'Purchase Order',
        self::FINISH_GOODS_TYPE => 'Finish Goods Conversion',
        self::GIN_TYPE => 'GIN',
        self::OPENING_TYPE => 'OPEN',
        self::GOOD_RECEIPT_TYPE => 'GIN',
        self::RETURN_TYPE => 'Return',
        self::ITEM_ADJ_TYPE => 'Item Adjustment',
        self::ITEM_ASSEMBLY_TYPE => 'Item Assembly Convert to QtyOnStock',
    ];

    const TRANSACTION_TYPE_MODEL_ARRAY = [
        self::REQUISITION_TYPE => 'App\Models\Requisition',
        self::INVENTORY_TRANSFER_TYPE => 'App\Models\Transfer',
        self::PURCHASE_ORDER_TYPE => 'App\Models\Order',
        self::FINISH_GOODS_TYPE => 'App\Models\FinishGoods',
        self::GIN_TYPE => 'App\Models\GIN',
        self::OPENING_TYPE => null,
        self::GOOD_RECEIPT_TYPE => 'App\Models\GoodsReceipt',
        self::RETURN_TYPE => 'App\Models\ReturnGIN',
        self::ITEM_ADJ_TYPE => 'App\Models\StockAdjustment',
        self::ITEM_ASSEMBLY_TYPE => null,
    ];

    const TRANSACTION_TYPE_MODEL_CODE_COL_ARRAY = [
        self::REQUISITION_TYPE => 'code',
        self::INVENTORY_TRANSFER_TYPE => 'code',
        self::PURCHASE_ORDER_TYPE => 'code',
        self::FINISH_GOODS_TYPE => 'code',
        self::GIN_TYPE => 'code',
        self::OPENING_TYPE => null,
        self::GOOD_RECEIPT_TYPE => 'code',
        self::RETURN_TYPE => 'gni_item_id',
        self::ITEM_ADJ_TYPE => 'code',
        self::ITEM_ASSEMBLY_TYPE => null,
    ];

    const TRANSACTION_TYPE_ROUTE_ARRAY = [
        self::REQUISITION_TYPE => 'requisitions',
        self::INVENTORY_TRANSFER_TYPE => 'transfers',
        self::PURCHASE_ORDER_TYPE => 'orders',
        self::FINISH_GOODS_TYPE => 'finish-goods',
        self::GIN_TYPE => 'goods-issue-notes',
        self::OPENING_TYPE => null,
        self::GOOD_RECEIPT_TYPE => 'goods-receipts',
        self::RETURN_TYPE => null,
        self::ITEM_ADJ_TYPE => 'stock-adjustments',
        self::ITEM_ASSEMBLY_TYPE => null,
    ];

    const TRANSACTION_TYPE_ARRAY_SELECT = [
        [
            'id' => self::REQUISITION_TYPE,
            'text' => self::TRANSACTION_TYPE_ARRAY[self::REQUISITION_TYPE],
        ],
        [
            'id' => self::INVENTORY_TRANSFER_TYPE,
            'text' => self::TRANSACTION_TYPE_ARRAY[self::INVENTORY_TRANSFER_TYPE],
        ],
        [
            'id' => self::PURCHASE_ORDER_TYPE,
            'text' => self::TRANSACTION_TYPE_ARRAY[self::PURCHASE_ORDER_TYPE],
        ],
        [
            'id' => self::FINISH_GOODS_TYPE,
            'text' => self::TRANSACTION_TYPE_ARRAY[self::FINISH_GOODS_TYPE],
        ],
        [
            'id' => self::GIN_TYPE,
            'text' => self::TRANSACTION_TYPE_ARRAY[self::GIN_TYPE],
        ],
        [
            'id' => self::OPENING_TYPE,
            'text' => self::TRANSACTION_TYPE_ARRAY[self::OPENING_TYPE],
        ],
        [
            'id' => self::GOOD_RECEIPT_TYPE,
            'text' => self::TRANSACTION_TYPE_ARRAY[self::GOOD_RECEIPT_TYPE],
        ],
        [
            'id' => self::RETURN_TYPE,
            'text' => self::TRANSACTION_TYPE_ARRAY[self::RETURN_TYPE],
        ],
    ];

    protected $appends = [
        'transaction_type_model_text',
        'transaction_type_route',
        'transaction_type_text'
    ];

    public function getTransactionTypeTextAttribute(): string
    {
        return self::TRANSACTION_TYPE_ARRAY[$this->transaction_type] ?? (string)$this->transaction_type;
    }

    public function getTransactionTypeModelTextAttribute()
    {
        return self::TRANSACTION_TYPE_MODEL_ARRAY[$this->transaction_type];
    }

    public function getTransactionTypeRouteAttribute()
    {
        return self::TRANSACTION_TYPE_ROUTE_ARRAY[$this->transaction_type] ?? null;
    }


    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transaction()
    {
        $modelClass = self::TRANSACTION_TYPE_MODEL_ARRAY[$this->transaction_type] ?? null;

        if ($modelClass) {
            if ($this->transaction_id) {
                return $modelClass::where('id', $this->transaction_id)->first();
            } elseif ($this->transaction_code) {
                return $modelClass::where(self::TRANSACTION_TYPE_MODEL_CODE_COL_ARRAY[$this->transaction_type], $this->transaction_code)->first();
            }
        }

        return $modelClass;
    }
}
