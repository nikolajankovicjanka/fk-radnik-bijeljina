<?php

namespace App\Services\Shop;

use App\Models\ShopOrder;
use App\Models\ShopProductVariant;
use App\Models\ShopStockMovement;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ShopOrderStatusService
{
    /**
     * Dozvoljene promjene statusa narudžbe.
     */
    private const ALLOWED_TRANSITIONS = [
        ShopOrder::STATUS_PENDING => [
            ShopOrder::STATUS_CONFIRMED,
            ShopOrder::STATUS_CANCELLED,
        ],

        ShopOrder::STATUS_CONFIRMED => [
            ShopOrder::STATUS_PROCESSING,
            ShopOrder::STATUS_CANCELLED,
        ],

        ShopOrder::STATUS_PROCESSING => [
            ShopOrder::STATUS_SHIPPED,
            ShopOrder::STATUS_CANCELLED,
        ],

        ShopOrder::STATUS_SHIPPED => [
            ShopOrder::STATUS_DELIVERED,
        ],

        ShopOrder::STATUS_DELIVERED => [],

        ShopOrder::STATUS_CANCELLED => [],
    ];

    public function changeStatus(
        ShopOrder $order,
        string    $newStatus
    ): ShopOrder
    {
        return DB::transaction(function () use (
            $order,
            $newStatus
        ): ShopOrder {
            /*
             * Ponovo dohvatamo narudžbu i zaključavamo je.
             *
             * Time sprečavamo da dva admin zahtjeva istovremeno
             * promijene status iste narudžbe.
             */
            $order = ShopOrder::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($order->id);

            $currentStatus = $order->status;

            /*
             * Ako je već na traženom statusu, ne radimo ništa.
             */
            if ($currentStatus === $newStatus) {
                return $order;
            }

            $this->validateTransition(
                $currentStatus,
                $newStatus
            );

            /*
             * PENDING -> CONFIRMED
             *
             * Rezervisana roba postaje stvarno prodata:
             *
             * stock_quantity    -= quantity
             * reserved_quantity -= quantity
             */
            if (
                $currentStatus === ShopOrder::STATUS_PENDING
                && $newStatus === ShopOrder::STATUS_CONFIRMED
            ) {
                $this->confirmOrderStock($order);
            }

            /*
             * PENDING -> CANCELLED
             *
             * Roba još nije bila fizički skinuta sa lagera.
             * Samo oslobađamo rezervaciju.
             */
            if (
                $currentStatus === ShopOrder::STATUS_PENDING
                && $newStatus === ShopOrder::STATUS_CANCELLED
            ) {
                $this->releaseReservedStock($order);
            }

            /*
             * CONFIRMED / PROCESSING -> CANCELLED
             *
             * Roba je već bila skinuta sa fizičkog lagera,
             * pa je vraćamo nazad.
             */
            if (
                in_array(
                    $currentStatus,
                    [
                        ShopOrder::STATUS_CONFIRMED,
                        ShopOrder::STATUS_PROCESSING,
                    ],
                    true
                )
                && $newStatus === ShopOrder::STATUS_CANCELLED
            ) {
                $this->restoreSoldStock($order);
            }

            $order->status = $newStatus;

            /*
             * Popunjavamo statusne timestampove ako postoje
             * u modelu/migraciji.
             */
            if (
                $newStatus === ShopOrder::STATUS_CONFIRMED
                && $order->confirmed_at === null
            ) {
                $order->confirmed_at = now();
            }

            if (
                $newStatus === ShopOrder::STATUS_SHIPPED
                && $order->shipped_at === null
            ) {
                $order->shipped_at = now();
            }

            if (
                $newStatus === ShopOrder::STATUS_DELIVERED
                && $order->delivered_at === null
            ) {
                $order->delivered_at = now();
            }

            if (
                $newStatus === ShopOrder::STATUS_CANCELLED
                && $order->cancelled_at === null
            ) {
                $order->cancelled_at = now();
            }

            $order->save();

            /*
             * Ako je narudžba otkazana i koristila je vaučer,
             * oslobađamo njegovo korištenje.
             *
             * Tako otkazana narudžba ne troši usage_limit.
             */
            if ($newStatus === ShopOrder::STATUS_CANCELLED) {
                $order->voucherUsages()->delete();
            }

            return $order->fresh([
                'items',
                'seasonTicket',
                'voucher',
            ]);
        }, 3);
    }

    private function validateTransition(
        string $currentStatus,
        string $newStatus
    ): void
    {
        if (!array_key_exists(
            $currentStatus,
            self::ALLOWED_TRANSITIONS
        )) {
            throw new InvalidArgumentException(
                "Nepoznat trenutni status narudžbe: {$currentStatus}."
            );
        }

        if (!in_array(
            $newStatus,
            self::ALLOWED_TRANSITIONS[$currentStatus],
            true
        )) {
            throw new InvalidArgumentException(
                "Promjena statusa sa {$currentStatus} "
                . "na {$newStatus} nije dozvoljena."
            );
        }
    }

    private function confirmOrderStock(
        ShopOrder $order
    ): void
    {
        $quantities = $this->getVariantQuantities($order);

        foreach ($quantities as $variantId => $quantity) {
            $variant = ShopProductVariant::query()
                ->lockForUpdate()
                ->find($variantId);

            if (!$variant) {
                throw new InvalidArgumentException(
                    "Varijanta proizvoda ID {$variantId} više ne postoji."
                );
            }

            if (
                (int)$variant->reserved_quantity
                < $quantity
            ) {
                throw new InvalidArgumentException(
                    "Nedovoljna rezervisana količina za "
                    . "varijantu {$variant->sku}."
                );
            }

            if (
                (int)$variant->stock_quantity
                < $quantity
            ) {
                throw new InvalidArgumentException(
                    "Nedovoljno fizičkog stanja za "
                    . "varijantu {$variant->sku}."
                );
            }

            $before = (int)$variant->stock_quantity;
            $after = $before - $quantity;

            $variant->stock_quantity = $after;

            $variant->reserved_quantity =
                (int)$variant->reserved_quantity
                - $quantity;

            $variant->save();

            ShopStockMovement::create([
                'shop_product_variant_id' => $variant->id,
                'type' => ShopStockMovement::TYPE_SALE,
                'quantity' => -$quantity,
                'quantity_before' => $before,
                'quantity_after' => $after,
                'reference' => $order->order_number,
                'note' => 'Prodaja po narudžbi '
                    . $order->order_number,
                'created_by' => auth()->id(),
            ]);
        }
    }

    private function releaseReservedStock(
        ShopOrder $order
    ): void
    {
        $quantities = $this->getVariantQuantities($order);

        foreach ($quantities as $variantId => $quantity) {
            $variant = ShopProductVariant::query()
                ->lockForUpdate()
                ->find($variantId);

            if (!$variant) {
                throw new InvalidArgumentException(
                    "Varijanta proizvoda ID {$variantId} više ne postoji."
                );
            }

            if (
                (int)$variant->reserved_quantity
                < $quantity
            ) {
                throw new InvalidArgumentException(
                    "Nedovoljna rezervisana količina za "
                    . "varijantu {$variant->sku}."
                );
            }

            $variant->reserved_quantity =
                (int)$variant->reserved_quantity
                - $quantity;

            $variant->save();
        }
    }

    private function restoreSoldStock(
        ShopOrder $order
    ): void
    {
        $quantities = $this->getVariantQuantities($order);

        foreach ($quantities as $variantId => $quantity) {
            $variant = ShopProductVariant::query()
                ->lockForUpdate()
                ->find($variantId);

            if (!$variant) {
                throw new InvalidArgumentException(
                    "Varijanta proizvoda ID {$variantId} više ne postoji."
                );
            }

            $before = (int)$variant->stock_quantity;
            $after = $before + $quantity;

            $variant->stock_quantity = $after;
            $variant->save();

            ShopStockMovement::create([
                'shop_product_variant_id' => $variant->id,
                'type' => ShopStockMovement::TYPE_CANCELLATION,
                'quantity' => $quantity,
                'quantity_before' => $before,
                'quantity_after' => $after,
                'reference' => $order->order_number,
                'note' => 'Povrat na stanje zbog otkazivanja narudžbe '
                    . $order->order_number,
                'created_by' => auth()->id(),
            ]);
        }
    }

    private function getVariantQuantities(
        ShopOrder $order
    ): array
    {
        $quantities = [];

        foreach ($order->items as $item) {
            /*
             * Ako je proizvod/varijanta kasnije obrisana,
             * order item snapshot ostaje, ali lager više
             * nije moguće automatski mijenjati.
             */
            if (!$item->shop_product_variant_id) {
                throw new InvalidArgumentException(
                    "Narudžba {$order->order_number} sadrži "
                    . 'stavku bez povezane varijante proizvoda.'
                );
            }

            $variantId =
                (int)$item->shop_product_variant_id;

            $quantities[$variantId] =
                ($quantities[$variantId] ?? 0)
                + (int)$item->quantity;
        }

        return $quantities;
    }
}
