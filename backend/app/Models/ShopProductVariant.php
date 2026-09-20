<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_product_id',
        'size',
        'sku',
        'stock_quantity',
        'reserved_quantity',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'shop_product_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(ShopStockMovement::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(ShopOrderItem::class);
    }

    public function getAvailableQuantityAttribute(): int
    {
        return max(
            0,
            $this->stock_quantity - $this->reserved_quantity
        );
    }

    public function getInStockAttribute(): bool
    {
        return $this->is_active && $this->available_quantity > 0;
    }
}
