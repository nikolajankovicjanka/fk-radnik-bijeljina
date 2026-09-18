<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\CalculateShopPriceRequest;
use App\Models\ShopProductVariant;
use App\Services\Shop\ShopPricingService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Throwable;

class ShopPricingController extends Controller
{
    public function __construct(
        private readonly ShopPricingService $pricingService,
    )
    {
    }

    public function calculate(
        CalculateShopPriceRequest $request
    ): JsonResponse
    {
        try {
            $data = $request->validated();

            $requestedQuantities = [];

            foreach ($data['items'] as $item) {
                $variantId = (int)$item['variant_id'];
                $quantity = (int)$item['quantity'];

                $requestedQuantities[$variantId] =
                    ($requestedQuantities[$variantId] ?? 0)
                    + $quantity;
            }

            $variants = ShopProductVariant::query()
                ->with('product')
                ->whereIn(
                    'id',
                    array_keys($requestedQuantities)
                )
                ->get()
                ->keyBy('id');

            $subtotal = 0.0;

            foreach ($requestedQuantities as $variantId => $quantity) {
                $variant = $variants->get($variantId);

                if (!$variant) {
                    throw new InvalidArgumentException(
                        'Odabrana varijanta proizvoda ne postoji.'
                    );
                }

                if (!$variant->is_active) {
                    throw new InvalidArgumentException(
                        "Varijanta {$variant->sku} trenutno nije dostupna."
                    );
                }

                $product = $variant->product;

                if (!$product || !$product->is_active) {
                    throw new InvalidArgumentException(
                        'Odabrani proizvod trenutno nije dostupan.'
                    );
                }

                $availableQuantity = max(
                    0,
                    (int)$variant->stock_quantity
                    - (int)$variant->reserved_quantity
                );

                if ($availableQuantity < $quantity) {
                    throw new InvalidArgumentException(
                        "Nema dovoljno proizvoda {$product->name} "
                        . "({$variant->size}) na stanju."
                    );
                }

                /*
                 * Cijenu uzimamo isključivo iz baze.
                 */
                $unitPrice = (float)(
                    $product->sale_price ?? $product->price
                );

                $subtotal += $unitPrice * $quantity;
            }

            $subtotal = round($subtotal, 2);

            $pricing = $this->pricingService->calculate(
                subtotal: $subtotal,

                seasonTicketNumber: $data['season_ticket_number'] ?? null,

                voucherCode: $data['voucher_code'] ?? null,

                deliveryMethod: $data['delivery_method'],
            );

            return response()->json([
                'success' => true,

                'data' => [
                    'subtotal' =>
                        $pricing['subtotal'],

                    'discount_type' =>
                        $pricing['discount_type'],

                    'discount_percent' =>
                        $pricing['discount_percent'],

                    'discount_amount' =>
                        $pricing['discount_amount'],

                    'shipping_amount' =>
                        $pricing['shipping_amount'],

                    'total' =>
                        $pricing['total'],

                    'season_ticket' =>
                        $pricing['season_ticket']
                            ? [
                            'valid' => true,

                            'ticket_number' =>
                                $pricing['season_ticket']
                                    ->ticket_number,
                        ]
                            : null,

                    'voucher' =>
                        $pricing['voucher']
                            ? [
                            'valid' => true,

                            'code' =>
                                $pricing['voucher']->code,

                            'name' =>
                                $pricing['voucher']->name,
                        ]
                            : null,
                ],
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' =>
                    'Došlo je do greške prilikom obračuna narudžbe.',
            ], 500);
        }
    }
}
