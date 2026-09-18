<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\CreateShopOrderRequest;
use App\Services\Shop\ShopCheckoutService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Throwable;

class ShopOrderController extends Controller
{
    public function __construct(
        private readonly ShopCheckoutService $checkoutService,
    )
    {
    }

    public function store(
        CreateShopOrderRequest $request
    ): JsonResponse
    {
        try {
            $order = $this->checkoutService->createOrder(
                $request->validated()
            );

            return response()->json([
                'success' => true,

                'message' =>
                    'Narudžba je uspješno zaprimljena.',

                'data' => [
                    'order_number' =>
                        $order->order_number,

                    'status' =>
                        $order->status,

                    'subtotal' =>
                        (float)$order->subtotal,

                    'discount_type' =>
                        $order->discount_type,

                    'discount_percent' =>
                        $order->discount_percent !== null
                            ? (float)$order->discount_percent
                            : null,

                    'discount_amount' =>
                        (float)$order->discount_amount,

                    'shipping_amount' =>
                        (float)$order->shipping_amount,

                    'total' =>
                        (float)$order->total,

                    'payment_method' =>
                        $order->payment_method,

                    'delivery_method' =>
                        $order->delivery_method,
                ],
            ], 201);
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
                    'Došlo je do greške prilikom kreiranja narudžbe.',
            ], 500);
        }
    }
}
