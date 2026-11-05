<?php

namespace App\Http\Controllers\Api\Shop;

use App\Models\ShopSetting;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\CreateShopOrderRequest;
use App\Mail\Shop\AdminNewOrderNotification;
use App\Mail\Shop\CustomerOrderConfirmation;
use App\Models\ShopOrder;
use App\Services\Shop\ShopCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
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
         * Primalac administratorskog obavještenja.
         *
         * Prioritet:
         * 1. shop_email iz baze
         * 2. SHOP_ADMIN_EMAIL iz konfiguracije
         */
        try {
            $adminEmail = trim(
                (string)ShopSetting::getValue('shop_email', '')
            );

            if ($adminEmail === '') {
                $adminEmail = trim(
                    (string)config('shop.admin_email', '')
                );
            }
        } catch (Throwable $exception) {
            report($exception);

            Log::error('Webshop admin email configuration failed', [
                'order_id' => $order->id,
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
            ]);

            $adminEmail = trim(
                (string)config('shop.admin_email', '')
            );
        }

        /*
         * Priprema mailova.
         *
         * Customer i admin obavještenja obrađuju se
         * odvojeno kako greška jednog ne bi spriječila drugi.
         */
        $notifications = [
            [
                'type' => 'customer',
                'recipient' => $order->email,
                'mailable' => new CustomerOrderConfirmation($order),
            ],
        ];

        if ($adminEmail !== '') {
            $notifications[] = [
                'type' => 'admin',
                'recipient' => $adminEmail,
                'mailable' => new AdminNewOrderNotification($order),
            ];
        } else {
            Log::warning('Webshop admin email is not configured', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
        }

        /*
         * Slanje mailova i detaljno logovanje.
         *
         * Kod QUEUE_CONNECTION=sync slanje se izvršava
         * odmah, unutar HTTP zahtjeva.
         *
         * Kod QUEUE_CONNECTION=database/redis,
         * queue() samo dodaje posao u red.
         */
        foreach ($notifications as $notification) {
            $startedAt = microtime(true);

            Log::info('Webshop email attempt started', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'type' => $notification['type'],
                'queue' => config('queue.default'),
                'mailer' => config('mail.default'),
                'smtp_host' => config('mail.mailers.smtp.host'),
                'smtp_port' => config('mail.mailers.smtp.port'),
            ]);

            try {
                Mail::to($notification['recipient'])
                    ->queue(
                        $notification['mailable']->afterCommit()
                    );

                Log::info('Webshop email attempt completed', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'type' => $notification['type'],
                    'duration_ms' => round(
                        (microtime(true) - $startedAt) * 1000
                    ),
                    'queue' => config('queue.default'),
                ]);
            } catch (Throwable $exception) {
                report($exception);

                Log::error('Webshop email attempt failed', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'type' => $notification['type'],
                    'duration_ms' => round(
                        (microtime(true) - $startedAt) * 1000
                    ),
                    'exception' => get_class($exception),
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}
