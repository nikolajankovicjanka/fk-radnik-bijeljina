<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $spa = public_path('dist/index.html');
    if (!file_exists($spa)) {
        $spa = public_path('index.html');
    }

    abort_unless(file_exists($spa), 500, 'SPA index.html not found in public.');

    return response()->file($spa);
});

Route::get('/{any}', function () {
    $spa = public_path('dist/index.html');
    if (!file_exists($spa)) {
        $spa = public_path('index.html');
    }

    abort_unless(file_exists($spa), 500, 'SPA index.html not found in public.');

    return response()->file($spa);
})->where('any', '^(?!api|admin|livewire|storage|build|dist|vendor).*$');
