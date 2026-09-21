<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_orders', function (Blueprint $table) {
            $table->string('address')->nullable()->change();
            $table->string('city', 100)->nullable()->change();
            $table->string('postal_code', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('shop_orders', function (Blueprint $table) {
            $table->string('address')->nullable(false)->change();
            $table->string('city', 100)->nullable(false)->change();
            $table->string('postal_code', 20)->nullable(false)->change();
        });
    }
};
