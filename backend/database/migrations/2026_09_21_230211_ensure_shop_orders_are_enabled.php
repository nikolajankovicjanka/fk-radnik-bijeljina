<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('shop_settings')
            ->where('key', 'orders_enabled')
            ->exists();

        if ($exists) {
            DB::table('shop_settings')
                ->where('key', 'orders_enabled')
                ->update([
                    'value' => '1',
                    'type' => 'boolean',
                    'updated_at' => now(),
                ]);

            return;
        }

        DB::table('shop_settings')->insert([
            'key' => 'orders_enabled',
            'value' => '1',
            'type' => 'boolean',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        //
    }
};
