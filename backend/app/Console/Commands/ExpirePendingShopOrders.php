<?php

namespace App\Console\Commands;

use App\Models\ShopOrder;
use App\Models\ShopSetting;
use App\Services\Shop\ShopOrderStatusService;
use Illuminate\Console\Command;
use Throwable;

class ExpirePendingShopOrders extends Command
{
    protected $signature = 'shop:expire-pending-orders';

    protected $description =
        'Otkazuje istekle pending webshop narudžbe i oslobađa rezervisani lager.';

    public function __construct(
        private readonly ShopOrderStatusService $orderStatusService,
    )
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $expiryHours = (int)ShopSetting::getValue(
            'pending_order_expiry_hours',
            24
        );

        /*
         * Vrijednost 0 ili manje isključuje automatsko
         * isticanje pending narudžbi.
         */
        if ($expiryHours <= 0) {
            $this->info(
                'Automatsko isticanje pending narudžbi je isključeno.'
            );

            return self::SUCCESS;
        }

        $expiresBefore = now()->subHours($expiryHours);

        $expiredOrders = ShopOrder::query()
            ->where('status', ShopOrder::STATUS_PENDING)
            ->where('created_at', '<=', $expiresBefore)
            ->orderBy('id')
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info(
                'Nema isteklih pending narudžbi.'
            );

            return self::SUCCESS;
        }

        $cancelled = 0;
        $failed = 0;

        foreach ($expiredOrders as $order) {
            try {
                /*
                 * Ne mijenjamo stock i reserved_quantity direktno.
                 *
                 * Sve prolazi kroz centralni status servis,
                 * koji već zna kako se oslobađa rezervacija
                 * i voucher usage.
                 */
                $this->orderStatusService->changeStatus(
                    $order,
                    ShopOrder::STATUS_CANCELLED
                );

                $cancelled++;

                $this->line(
                    "Otkazana narudžba: {$order->order_number}"
                );
            } catch (Throwable $exception) {
                $failed++;

                report($exception);

                $this->error(
                    "Greška kod narudžbe {$order->order_number}: "
                    . $exception->getMessage()
                );
            }
        }

        $this->newLine();

        $this->info(
            "Završeno. Otkazano: {$cancelled}, greške: {$failed}."
        );

        return $failed > 0
            ? self::FAILURE
            : self::SUCCESS;
    }
}
