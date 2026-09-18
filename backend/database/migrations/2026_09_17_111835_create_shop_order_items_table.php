<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shop_order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shop_order_id')
                ->constrained('shop_orders')
                ->cascadeOnDelete();

            $table->foreignId('shop_product_id')
                ->nullable()
                ->constrained('shop_products')
                ->nullOnDelete();

            $table->foreignId('shop_product_variant_id')
                ->nullable()
                ->constrained('shop_product_variants')
                ->nullOnDelete();

            // Snapshot proizvoda u trenutku kupovine.
            $table->string('product_name');
            $table->string('sku');
            $table->string('size', 30)->nullable();

            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('line_total', 10, 2);

            // Personalizacija dresa.
            $table->string('customization_name')->nullable();
            $table->string('customization_number', 10)->nullable();
            $table->decimal('customization_price', 10, 2)->default(0);

            $table->timestamps();

            $table->index('shop_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_order_items');
    }
};
