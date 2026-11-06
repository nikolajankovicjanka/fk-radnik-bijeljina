<?php

namespace App\Providers;

use App\Mail\Transport\ZohoTransport;
use App\Services\Mail\ZohoMailService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Mail::extend('zoho', function (array $config = []) {
            return new ZohoTransport(
                app(ZohoMailService::class)
            );
        });
    }
}
