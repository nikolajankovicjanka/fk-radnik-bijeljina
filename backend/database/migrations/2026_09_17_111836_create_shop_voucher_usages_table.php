<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shop_voucher_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shop_voucher_id')
                ->constrained('shop_vouchers')
                ->cascadeOnDelete();

            $table->foreignId('shop_order_id')
                ->constrained('shop_orders')
                ->cascadeOnDelete();

            $table->decimal('discount_amount', 10, 2);

            $table->timestamp('used_at');

            $table->timestamps();

            // Isti voucher se na istoj narudžbi ne može evidentirati dva puta.
            $table->unique(['shop_voucher_id', 'shop_order_id']);

            $table->index('used_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_voucher_usages');
    }
};
