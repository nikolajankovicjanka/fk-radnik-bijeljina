<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        /*
         * Svakih 15 minuta provjeravamo postoje li pending
         * webshop narudžbe kojima je istekla rezervacija.
         *
         * withoutOverlapping sprečava pokretanje nove instance
         * dok prethodna još uvijek radi.
         */
        $schedule
            ->command('shop:expire-pending-orders')
            ->everyFifteenMinutes()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
