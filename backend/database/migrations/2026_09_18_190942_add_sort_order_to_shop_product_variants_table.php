<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shop_product_variants', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')
                ->default(0)
                ->after('is_active');

            $table->index(
                ['shop_product_id', 'sort_order'],
                'shop_product_variants_product_sort_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('shop_product_variants', function (Blueprint $table) {
            $table->dropIndex('shop_product_variants_product_sort_index');
            $table->dropColumn('sort_order');
        });
    }
};
