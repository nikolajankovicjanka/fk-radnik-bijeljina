<?php

namespace App\Services\Shop;

use App\Models\ShopOrder;
use App\Models\ShopSetting;
use InvalidArgumentException;

class ShopPricingService
{
    public function __construct(
        private readonly SeasonTicketService $seasonTicketService,
        private readonly VoucherService      $voucherService,
    )
    {
    }

    /**
     * Računa konačnu cijenu narudžbe.
     *
     * Sezonska karta i promo vaučer se ne mogu kombinovati.
     */
    public function calculate(
        float   $subtotal,
        ?string $seasonTicketNumber = null,
        ?string $voucherCode = null,
        string  $deliveryMethod = ShopOrder::DELIVERY_COURIER
    ): array
    {
        $subtotal = round(max(0, $subtotal), 2);

        if ($subtotal <= 0) {
            throw new InvalidArgumentException(
                'Vrijednost narudžbe mora biti veća od 0.'
            );
        }

        $seasonTicketNumber = trim((string)$seasonTicketNumber);
        $voucherCode = trim((string)$voucherCode);

        if ($seasonTicketNumber !== '' && $voucherCode !== '') {
            throw new InvalidArgumentException(
                'Sezonska karta i promo vaučer se ne mogu kombinovati.'
            );
        }

        $discountType = null;
        $discountPercent = null;
        $discountAmount = 0.0;
        $seasonTicket = null;
        $voucher = null;

        if ($seasonTicketNumber !== '') {
            $result = $this->seasonTicketService->validate(
                $seasonTicketNumber
            );

            if (!$result['valid']) {
                throw new InvalidArgumentException($result['message']);
            }

            $seasonTicket = $result['ticket'];
            $discountPercent = (float)$result['discount_percent'];

            $discountAmount = $this->seasonTicketService
                ->calculateDiscount(
                    $subtotal,
                    $discountPercent
                );

            $discountType = ShopOrder::DISCOUNT_SEASON_TICKET;
        }

        if ($voucherCode !== '') {
            $result = $this->voucherService->validate(
                $voucherCode,
                $subtotal
            );

            if (!$result['valid']) {
                throw new InvalidArgumentException($result['message']);
            }

            $voucher = $result['voucher'];
            $discountAmount = (float)$result['discount_amount'];

            $discountType = ShopOrder::DISCOUNT_VOUCHER;

            if (
                $voucher->discount_type === 'percentage'
            ) {
                $discountPercent = (float)$voucher->discount_value;
            }
        }

        $shippingAmount = $this->calculateShipping(
            $subtotal,
            $deliveryMethod
        );

        $total = round(
            max(
                0,
                $subtotal - $discountAmount + $shippingAmount
            ),
            2
        );

        return [
            'subtotal' => $subtotal,

            'discount_type' => $discountType,
            'discount_percent' => $discountPercent,
            'discount_amount' => round($discountAmount, 2),

            'shipping_amount' => $shippingAmount,

            'total' => $total,

            'season_ticket' => $seasonTicket,
            'voucher' => $voucher,
        ];
    }

    private function calculateShipping(
        float  $subtotal,
        string $deliveryMethod
    ): float
    {
        if ($deliveryMethod === ShopOrder::DELIVERY_PICKUP) {
            return 0.0;
        }

        if ($deliveryMethod !== ShopOrder::DELIVERY_COURIER) {
            throw new InvalidArgumentException(
                'Nepoznat način dostave.'
            );
        }

        $shippingPrice = (float)ShopSetting::getValue(
            'shipping_price',
            0
        );

        $freeShippingThreshold = (float)ShopSetting::getValue(
            'free_shipping_threshold',
            0
        );

        if (
            $freeShippingThreshold > 0
            && $subtotal >= $freeShippingThreshold
        ) {
            return 0.0;
        }

        return round(max(0, $shippingPrice), 2);
    }
}
