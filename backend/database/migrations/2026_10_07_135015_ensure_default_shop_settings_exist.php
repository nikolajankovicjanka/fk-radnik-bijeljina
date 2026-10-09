<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $settings = [
            [
                'key' => 'current_season',
                'value' => '2026/27',
                'type' => 'string',
            ],
            [
                'key' => 'season_ticket_discount_percent',
                'value' => '20',
                'type' => 'decimal',
            ],
            [
                'key' => 'season_ticket_discount_enabled',
                'value' => '1',
                'type' => 'boolean',
            ],
            [
                'key' => 'shipping_price',
                'value' => '0',
                'type' => 'decimal',
            ],
            [
                'key' => 'free_shipping_threshold',
                'value' => '0',
                'type' => 'decimal',
            ],
            [
                'key' => 'shop_email',
                'value' => '',
                'type' => 'string',
            ],
            [
                'key' => 'orders_enabled',
                'value' => '1',
                'type' => 'boolean',
            ],
            [
                'key' => 'pending_order_expiry_hours',
                'value' => '24',
                'type' => 'integer',
            ],
        ];

        foreach ($settings as $setting) {
            $exists = DB::table('shop_settings')
                ->where('key', $setting['key'])
                ->exists();

            if (!$exists) {
                DB::table('shop_settings')->insert([
                    'key' => $setting['key'],
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
