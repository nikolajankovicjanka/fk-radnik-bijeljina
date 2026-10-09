<?php

return [
    'admin_email' => env(
        'SHOP_ADMIN_EMAIL',
        'webshop@fkradnikbijeljina.com'
    ),

    'asset_url' => env('SHOP_ASSET_URL', env('APP_URL')),
];
