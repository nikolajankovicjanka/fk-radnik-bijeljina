<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_order_id',
        'shop_product_id',
        'shop_product_variant_id',
        'product_name',
        'sku',
        'size',
        'unit_price',
        'quantity',
        'line_total',
        'customization_name',
        'customization_number',
        'customization_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'line_total' => 'decimal:2',
        'customization_price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(ShopOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            ShopProductVariant::class,
            'shop_product_variant_id'
        );
    }
}
