<?php

namespace Database\Seeders;

use App\Models\ShopCategory;
use App\Models\ShopSetting;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'current_season',
                'value' => '2026/27',
                'type' => 'string',
            ],
            [
                'key' => 'season_ticket_discount_enabled',
                'value' => '1',
                'type' => 'boolean',
            ],
            [
                'key' => 'season_ticket_discount_percent',
                'value' => '20',
                'type' => 'decimal',
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
                'key' => 'pending_order_expiry_hours',
                'value' => '24',
                'type' => 'integer',
            ],
            [
                'key' => 'orders_enabled',
                'value' => '1',
                'type' => 'boolean',
            ],
        ];

        foreach ($settings as $setting) {
            ShopSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                ]
            );
        }

        $categories = [
            [
                'name' => 'Dresovi',
                'slug' => 'dresovi',
                'sort_order' => 1,
            ],
            [
                'name' => 'Odjeća',
                'slug' => 'odjeca',
                'sort_order' => 2,
            ],
            [
                'name' => 'Navijački program',
                'slug' => 'navijacki-program',
                'sort_order' => 3,
            ],
            [
                'name' => 'Aksesoari',
                'slug' => 'aksesoari',
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            ShopCategory::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
