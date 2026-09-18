<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shop_vouchers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('code')->unique();

            $table->text('description')->nullable();

            // percentage | fixed
            $table->string('discount_type', 20)->default('percentage');
            $table->decimal('discount_value', 10, 2);

            $table->decimal('minimum_order_amount', 10, 2)->nullable();

            // NULL = neograničeno korištenje
            $table->unsignedInteger('usage_limit')->nullable();

            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_vouchers');
    }
};
