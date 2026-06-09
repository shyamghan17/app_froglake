<?php

namespace Workdo\OpticalAndEyeCareCenter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workdo\ProductService\Models\ProductServiceItem;

class EyewearOrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'item_type',
        'quantity',
        'unit_price',
        'discount_percentage',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'total_amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(EyewearOrder::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductServiceItem::class, 'product_id');
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(EyewearOrderItemTax::class, 'item_id');
    }

    public function eyewearItem(): BelongsTo
    {
        return $this->belongsTo(EyewearItem::class, 'product_id', 'product_id');
    }
}
