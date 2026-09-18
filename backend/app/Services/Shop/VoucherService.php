<?php

namespace App\Services\Shop;

use App\Models\ShopVoucher;
use Carbon\Carbon;

class VoucherService
{
    /**
     * Provjerava da li je promo vaučer validan za dati subtotal robe.
     */
    public function validate(?string $code, float $subtotal): array
    {
        $code = strtoupper(trim((string)$code));

        if ($code === '') {
            return $this->invalid('Promo kod nije unesen.');
        }

        if ($subtotal <= 0) {
            return $this->invalid('Vrijednost narudžbe mora biti veća od 0.');
        }

        $voucher = ShopVoucher::query()
            ->whereRaw('UPPER(code) = ?', [$code])
            ->first();

        if (!$voucher) {
            return $this->invalid('Promo kod nije pronađen.');
        }

        if (!$voucher->is_active) {
            return $this->invalid('Promo kod trenutno nije aktivan.');
        }

        $now = Carbon::now();

        if ($voucher->starts_at && $now->lt($voucher->starts_at)) {
            return $this->invalid('Promo kod još nije aktivan.');
        }

        if ($voucher->ends_at && $now->gt($voucher->ends_at)) {
            return $this->invalid('Promo kod je istekao.');
        }

        if (
            $voucher->minimum_order_amount !== null
            && $subtotal < (float)$voucher->minimum_order_amount
        ) {
            return $this->invalid(
                'Minimalna vrijednost narudžbe za ovaj promo kod je '
                . number_format(
                    (float)$voucher->minimum_order_amount,
                    2,
                    ',',
                    '.'
                )
                . ' KM.'
            );
        }

        if ($voucher->usage_limit !== null) {
            $usageCount = $voucher->usages()->count();

            if ($usageCount >= (int)$voucher->usage_limit) {
                return $this->invalid(
                    'Promo kod je dostigao maksimalan broj korištenja.'
                );
            }
        }

        $discountAmount = $this->calculateDiscount(
            $voucher,
            $subtotal
        );

        if ($discountAmount <= 0) {
            return $this->invalid(
                'Promo kod nema ispravno podešenu vrijednost popusta.'
            );
        }

        return [
            'valid' => true,
            'message' => 'Promo kod je prihvaćen.',
            'voucher' => $voucher,
            'discount_type' => $voucher->discount_type,
            'discount_value' => (float)$voucher->discount_value,
            'discount_amount' => $discountAmount,
        ];
    }

    /**
     * Računa popust, ali nikada ne dozvoljava da popust
     * bude veći od vrijednosti robe.
     */
    public function calculateDiscount(
        ShopVoucher $voucher,
        float       $subtotal
    ): float
    {
        if ($subtotal <= 0) {
            return 0.0;
        }

        $value = (float)$voucher->discount_value;

        if ($value <= 0) {
            return 0.0;
        }

        $discount = match ($voucher->discount_type) {
            ShopVoucher::TYPE_PERCENTAGE =>
                $subtotal * (min($value, 100) / 100),

            ShopVoucher::TYPE_FIXED =>
            $value,

            default =>
            0.0,
        };

        return round(
            min($discount, $subtotal),
            2
        );
    }

    private function invalid(string $message): array
    {
        return [
            'valid' => false,
            'message' => $message,
            'voucher' => null,
            'discount_type' => null,
            'discount_value' => 0.0,
            'discount_amount' => 0.0,
        ];
    }
}
