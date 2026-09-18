<?php

namespace App\Services\Shop;

use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopProductVariant;
use App\Models\ShopSetting;
use App\Models\ShopVoucherUsage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ShopCheckoutService
{
    public function __construct(
        private readonly ShopPricingService $pricingService,
    )
    {
    }

    public function createOrder(array $data): ShopOrder
    {
        $ordersEnabled = (bool)ShopSetting::getValue(
            'orders_enabled',
            false
        );

        if (!$ordersEnabled) {
            throw new InvalidArgumentException(
                'Webshop trenutno ne prima narudžbe.'
            );
        }

        $this->validateCustomerData($data);

        if (empty($data['items']) || !is_array($data['items'])) {
            throw new InvalidArgumentException(
                'Narudžba mora sadržavati najmanje jedan artikal.'
            );
        }

        /*
         * Normalizujemo korpu prije ulaska u transakciju.
         *
         * Ista varijanta sa istom personalizacijom se spaja u jednu stavku.
         * Ista varijanta sa različitim personalizacijama ostaje odvojena.
         */
        $data['items'] = $this->normalizeItems($data['items']);

        /*
         * Računamo ukupno traženu količinu po varijanti.
         *
         * Ovo je važno jer možemo imati npr:
         *
         * S / JANKOVIĆ / 10 = 6 komada
         * S / PETROVIĆ / 9  = 6 komada
         *
         * To su dvije različite order stavke, ali obje koriste
         * istu fizičku varijantu i zajedno traže 12 komada.
         */
        $requestedQuantities = [];

        foreach ($data['items'] as $item) {
            $variantId = (int)$item['variant_id'];

            $requestedQuantities[$variantId] =
                ($requestedQuantities[$variantId] ?? 0)
                + (int)$item['quantity'];
        }

        return DB::transaction(
            function () use ($data, $requestedQuantities): ShopOrder {
                $preparedItems = [];
                $subtotal = 0.0;

                /*
                 * Ovdje pamtimo već zaključane varijante.
                 *
                 * Ako ista varijanta postoji više puta zbog različite
                 * personalizacije, nema potrebe da je više puta
                 * ponovo dohvatamo i zaključavamo.
                 */
                $lockedVariants = [];

                foreach ($data['items'] as $item) {
                    $variantId = (int)$item['variant_id'];
                    $quantity = (int)$item['quantity'];

                    if (!isset($lockedVariants[$variantId])) {
                        $variant = ShopProductVariant::query()
                            ->with('product')
                            ->lockForUpdate()
                            ->find($variantId);

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

                        $requestedQuantity =
                            $requestedQuantities[$variantId];

                        if ($availableQuantity < $requestedQuantity) {
                            throw new InvalidArgumentException(
                                "Nema dovoljno proizvoda {$product->name} "
                                . "({$variant->size}) na stanju."
                            );
                        }

                        $lockedVariants[$variantId] = $variant;
                    }

                    /** @var ShopProductVariant $variant */
                    $variant = $lockedVariants[$variantId];

                    $product = $variant->product;

                    /*
                     * Cijenu nikada ne uzimamo sa frontenda.
                     * Backend uvijek koristi cijenu iz baze.
                     */
                    $unitPrice = (float)(
                        $product->sale_price ?? $product->price
                    );

                    /*
                     * Personalizacija trenutno nema aktivnu cijenu.
                     *
                     * Strukturu i podatke čuvamo u order item snapshotu,
                     * a cijenu ćemo uključiti kada definišemo pravilo
                     * za cijenu personalizacije.
                     */
                    $customizationName = null;
                    $customizationNumber = null;
                    $customizationPrice = 0.0;

                    if (
                        $product->is_customizable
                        && !empty($item['customization'])
                        && is_array($item['customization'])
                    ) {
                        $customizationName = isset(
                            $item['customization']['name']
                        )
                            ? trim(
                                (string)$item['customization']['name']
                            )
                            : null;

                        $customizationNumber = isset(
                            $item['customization']['number']
                        )
                            ? trim(
                                (string)$item['customization']['number']
                            )
                            : null;

                        $customizationName =
                            $customizationName !== ''
                                ? $customizationName
                                : null;

                        $customizationNumber =
                            $customizationNumber !== ''
                                ? $customizationNumber
                                : null;
                    }

                    $lineTotal = round(
                        ($unitPrice + $customizationPrice) * $quantity,
                        2
                    );

                    $subtotal += $lineTotal;

                    $preparedItems[] = [
                        'variant' => $variant,
                        'product' => $product,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'line_total' => $lineTotal,
                        'customization_name' => $customizationName,
                        'customization_number' => $customizationNumber,
                        'customization_price' => $customizationPrice,
                    ];
                }

                $subtotal = round($subtotal, 2);

                $seasonTicketNumber =
                    isset($data['season_ticket_number'])
                        ? trim(
                        (string)$data['season_ticket_number']
                    )
                        : null;

                $voucherCode =
                    isset($data['voucher_code'])
                        ? trim(
                        (string)$data['voucher_code']
                    )
                        : null;

                $deliveryMethod =
                    $data['delivery_method']
                    ?? ShopOrder::DELIVERY_COURIER;

                /*
                 * Pricing servis ponovo validira popuste.
                 *
                 * Frontend ne odlučuje:
                 * - cijenu
                 * - popust
                 * - dostavu
                 * - total
                 */
                $pricing = $this->pricingService->calculate(
                    $subtotal,
                    $seasonTicketNumber,
                    $voucherCode,
                    $deliveryMethod
                );

                $order = ShopOrder::create([
                    'order_number' => $this->generateOrderNumber(),

                    'first_name' => trim(
                        (string)($data['first_name'] ?? '')
                    ),

                    'last_name' => trim(
                        (string)($data['last_name'] ?? '')
                    ),

                    'email' => trim(
                        (string)($data['email'] ?? '')
                    ),

                    'phone' => trim(
                        (string)($data['phone'] ?? '')
                    ),

                    'address' => $this->nullableString(
                        $data['address'] ?? null
                    ),

                    'city' => $this->nullableString(
                        $data['city'] ?? null
                    ),

                    'postal_code' => $this->nullableString(
                        $data['postal_code'] ?? null
                    ),

                    'notes' => $this->nullableString(
                        $data['notes'] ?? null
                    ),

                    'status' => ShopOrder::STATUS_PENDING,

                    'payment_method' =>
                        ShopOrder::PAYMENT_COD,

                    'delivery_method' => $deliveryMethod,

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

                    'season_ticket_id' =>
                        $pricing['season_ticket']?->id,

                    'season_ticket_number' =>
                        $pricing['season_ticket']?->ticket_number,

                    'shop_voucher_id' =>
                        $pricing['voucher']?->id,

                    'voucher_code' =>
                        $pricing['voucher']?->code,
                ]);

                foreach ($preparedItems as $preparedItem) {
                    /** @var ShopProductVariant $variant */
                    $variant = $preparedItem['variant'];

                    ShopOrderItem::create([
                        'shop_order_id' => $order->id,

                        'shop_product_id' =>
                            $preparedItem['product']->id,

                        'shop_product_variant_id' =>
                            $variant->id,

                        'product_name' =>
                            $preparedItem['product']->name,

                        'sku' =>
                            $variant->sku,

                        'size' =>
                            $variant->size,

                        'unit_price' =>
                            $preparedItem['unit_price'],

                        'quantity' =>
                            $preparedItem['quantity'],

                        'line_total' =>
                            $preparedItem['line_total'],

                        'customization_name' =>
                            $preparedItem['customization_name'],

                        'customization_number' =>
                            $preparedItem['customization_number'],

                        'customization_price' =>
                            $preparedItem['customization_price'],
                    ]);
                }

                /*
                 * Rezervaciju radimo jednom po fizičkoj varijanti,
                 * koristeći ukupnu količinu svih order stavki.
                 */
                foreach (
                    $requestedQuantities
                    as $variantId => $quantity
                ) {
                    /** @var ShopProductVariant $variant */
                    $variant = $lockedVariants[$variantId];

                    $variant->increment(
                        'reserved_quantity',
                        $quantity
                    );
                }

                /*
                 * Ako je korišten promo vaučer,
                 * evidentiramo njegovo korištenje.
                 */
                if ($pricing['voucher']) {
                    ShopVoucherUsage::create([
                        'shop_voucher_id' =>
                            $pricing['voucher']->id,

                        'shop_order_id' =>
                            $order->id,

                        'discount_amount' =>
                            $pricing['discount_amount'],

                        'used_at' =>
                            now(),
                    ]);
                }

                return $order->load([
                    'items',
                    'seasonTicket',
                    'voucher',
                ]);
            },
            3
        );
    }

    private function validateCustomerData(array $data): void
    {
        foreach (
            [
                'first_name' => 'Ime',
                'last_name' => 'Prezime',
                'email' => 'Email',
                'phone' => 'Telefon',
            ]
            as $field => $label
        ) {
            if (
                trim(
                    (string)($data[$field] ?? '')
                ) === ''
            ) {
                throw new InvalidArgumentException(
                    "{$label} je obavezno polje."
                );
            }
        }

        $email = trim(
            (string)($data['email'] ?? '')
        );

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                'Email adresa nije ispravna.'
            );
        }

        $deliveryMethod =
            $data['delivery_method']
            ?? ShopOrder::DELIVERY_COURIER;

        if (
            !in_array(
                $deliveryMethod,
                [
                    ShopOrder::DELIVERY_COURIER,
                    ShopOrder::DELIVERY_PICKUP,
                ],
                true
            )
        ) {
            throw new InvalidArgumentException(
                'Nepoznat način dostave.'
            );
        }

        /*
         * Kod kurirske dostave adresa i grad su obavezni.
         *
         * Kod ličnog preuzimanja nisu potrebni.
         */
        if (
            $deliveryMethod === ShopOrder::DELIVERY_COURIER
        ) {
            foreach (
                [
                    'address' => 'Adresa',
                    'city' => 'Grad',
                ]
                as $field => $label
            ) {
                if (
                    trim(
                        (string)($data[$field] ?? '')
                    ) === ''
                ) {
                    throw new InvalidArgumentException(
                        "{$label} je obavezno polje za dostavu."
                    );
                }
            }
        }
    }

    private function normalizeItems(array $items): array
    {
        $normalized = [];

        foreach ($items as $item) {
            $variantId = (int)(
                $item['variant_id'] ?? 0
            );

            $quantity = (int)(
                $item['quantity'] ?? 0
            );

            if ($variantId <= 0 || $quantity <= 0) {
                throw new InvalidArgumentException(
                    'Neispravna stavka narudžbe.'
                );
            }

            $customization =
                $item['customization'] ?? null;

            $customizationName =
                is_array($customization)
                    ? trim(
                    (string)(
                        $customization['name']
                        ?? ''
                    )
                )
                    : '';

            $customizationNumber =
                is_array($customization)
                    ? trim(
                    (string)(
                        $customization['number']
                        ?? ''
                    )
                )
                    : '';

            /*
             * Ista varijanta + ista personalizacija
             * predstavljaju istu order stavku.
             */
            $key =
                $variantId
                . '|'
                . mb_strtoupper($customizationName)
                . '|'
                . $customizationNumber;

            if (!isset($normalized[$key])) {
                $normalized[$key] = [
                    'variant_id' => $variantId,
                    'quantity' => 0,
                ];

                if (
                    $customizationName !== ''
                    || $customizationNumber !== ''
                ) {
                    $normalized[$key]['customization'] = [
                        'name' => $customizationName,
                        'number' => $customizationNumber,
                    ];
                }
            }

            $normalized[$key]['quantity'] += $quantity;
        }

        return array_values($normalized);
    }

    private function generateOrderNumber(): string
    {
        do {
            $number =
                'FKR-'
                . now()->format('Y')
                . '-'
                . Str::upper(
                    Str::random(8)
                );
        } while (
            ShopOrder::query()
                ->where('order_number', $number)
                ->exists()
        );

        return $number;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string)$value);

        return $value !== ''
            ? $value
            : null;
    }
}
