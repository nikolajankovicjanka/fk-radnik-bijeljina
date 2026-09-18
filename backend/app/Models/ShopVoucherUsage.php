<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopVoucherUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_voucher_id',
        'shop_order_id',
        'discount_amount',
        'used_at',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'used_at' => 'datetime',
    ];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(ShopVoucher::class, 'shop_voucher_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(ShopOrder::class, 'shop_order_id');
    }
}
