<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shop_stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shop_product_variant_id')
                ->constrained('shop_product_variants')
                ->cascadeOnDelete();

            $table->string('type', 30);

            // Pozitivno za ulaz, negativno za izlaz.
            $table->integer('quantity');

            $table->unsignedInteger('quantity_before');
            $table->unsignedInteger('quantity_after');

            $table->string('reference')->nullable();
            $table->text('note')->nullable();

            // Kasnije možemo povezati sa Filament korisnikom.
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['shop_product_variant_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_stock_movements');
    }
};
