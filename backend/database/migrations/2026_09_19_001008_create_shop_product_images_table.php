<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shop_product_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shop_product_id')
                ->constrained('shop_products')
                ->cascadeOnDelete();

            $table->string('image_path');

            $table->unsignedTinyInteger('sort_order')
                ->default(0);

            $table->timestamps();

            $table->index(
                ['shop_product_id', 'sort_order'],
                'shop_product_images_product_sort_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_product_images');
    }
};
