<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\CreateShopOrderRequest;
use App\Mail\Shop\AdminNewOrderNotification;
use App\Mail\Shop\CustomerOrderConfirmation;
use App\Models\ShopOrder;
use App\Services\Shop\ShopCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
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
        /*
         * Kreiranje narudžbe je odvojeno od slanja emailova.
         *
         * Ako checkout ne uspije, vraćamo odgovarajuću grešku.
         * Ako checkout uspije, eventualni problem sa emailom
         * ne smije uticati na već kreiranu narudžbu.
         */
        try {
            $order = $this->checkoutService->createOrder(
                $request->validated()
            );
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

        /*
         * Narudžba je u ovom trenutku već uspješno kreirana
         * i DB transaction je završen.
         */
        $order->loadMissing('items');

        $this->queueOrderEmails($order);

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
    }

    private function queueOrderEmails(
        ShopOrder $order
    ): void
    {
        /*
         * Customer email.
         *
         * Mail greška se loguje, ali ne utiče na API odgovor
         * jer je narudžba već kreirana.
         */
        try {
            Mail::to($order->email)
                ->queue(
                    (new CustomerOrderConfirmation($order))
                        ->afterCommit()
                );
        } catch (Throwable $exception) {
            report($exception);
        }

        /*
         * Admin email je u posebnom try/catch bloku.
         *
         * Ako customer email ne uspije, i dalje pokušavamo
         * poslati admin obavještenje.
         */
        try {
            $adminEmail = config('shop.admin_email');

            if ($adminEmail) {
                Mail::to($adminEmail)
                    ->queue(
                        (new AdminNewOrderNotification($order))
                            ->afterCommit()
                    );
            }
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
