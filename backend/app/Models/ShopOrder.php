<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopOrder extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const DISCOUNT_SEASON_TICKET = 'season_ticket';
    public const DISCOUNT_VOUCHER = 'voucher';

    public const PAYMENT_COD = 'cod';

    public const DELIVERY_COURIER = 'courier';
    public const DELIVERY_PICKUP = 'pickup';

    protected $fillable = [
        'order_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'notes',
        'status',
        'payment_method',
        'delivery_method',
        'subtotal',
        'discount_type',
        'discount_percent',
        'discount_amount',
        'shipping_amount',
        'total',
        'season_ticket_id',
        'shop_voucher_id',
        'season_ticket_number',
        'voucher_code',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ShopOrderItem::class);
    }

    public function seasonTicket(): BelongsTo
    {
        return $this->belongsTo(SeasonTicket::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(ShopVoucher::class, 'shop_voucher_id');
    }

    public function voucherUsages(): HasMany
    {
        return $this->hasMany(ShopVoucherUsage::class);
    }
}
