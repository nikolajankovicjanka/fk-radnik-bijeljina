<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shop_orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number')->unique();

            // Customer
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');

            // Delivery
            $table->string('address');
            $table->string('city');
            $table->string('postal_code')->nullable();

            $table->text('notes')->nullable();

            // pending | confirmed | processing | shipped | delivered | cancelled
            $table->string('status', 30)->default('pending');

            // Za sada COD.
            $table->string('payment_method', 30)->default('cod');

            // courier | pickup, ako ostavimo preuzimanje na stadionu
            $table->string('delivery_method', 30)->default('courier');

            // Financial snapshot
            $table->decimal('subtotal', 10, 2);

            // none | season_ticket | voucher
            $table->string('discount_type', 30)->nullable();
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);

            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Samo jedan od ova dva smije biti primijenjen.
            $table->foreignId('season_ticket_id')
                ->nullable()
                ->constrained('season_tickets')
                ->nullOnDelete();

            $table->foreignId('shop_voucher_id')
                ->nullable()
                ->constrained('shop_vouchers')
                ->nullOnDelete();

            // Snapshot zbog istorije narudžbe.
            $table->string('season_ticket_number')->nullable();
            $table->string('voucher_code')->nullable();

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('email');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_orders');
    }
};
