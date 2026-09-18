<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopStockMovement extends Model
{
    use HasFactory;

    public const TYPE_INITIAL = 'initial';
    public const TYPE_RESTOCK = 'restock';
    public const TYPE_SALE = 'sale';
    public const TYPE_CANCELLATION = 'cancellation';
    public const TYPE_ADJUSTMENT = 'adjustment';

    protected $fillable = [
        'shop_product_variant_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reference',
        'note',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            ShopProductVariant::class,
            'shop_product_variant_id'
        );
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
