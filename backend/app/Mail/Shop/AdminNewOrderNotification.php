<?php

namespace App\Mail\Shop;

use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNewOrderNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ShopOrder $order,
    )
    {
    }

    public function build(): self
    {
        return $this
            ->subject(
                "Nova webshop narudžba - {$this->order->order_number}"
            )
            ->view('emails.shop.admin-new-order');
    }
}
