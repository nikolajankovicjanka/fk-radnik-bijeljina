<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shop_product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shop_product_id')
                ->constrained('shop_products')
                ->cascadeOnDelete();

            // S, M, L, XL, XXL, ONE_SIZE, 116, 128...
            $table->string('size', 30);

            // Svaka varijanta ima svoj SKU.
            $table->string('sku')->unique();

            // Fizičko stanje u magacinu.
            $table->unsignedInteger('stock_quantity')->default(0);

            // Količina vezana za aktivne narudžbe.
            $table->unsignedInteger('reserved_quantity')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['shop_product_id', 'size']);
            $table->index(['shop_product_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_product_variants');
    }
};
