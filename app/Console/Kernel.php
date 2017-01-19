<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'mobilecommons:fetch' => Commands\FetchUpdatesFromMobileCommons::class,
        'mobilecommons:backfill' => Commands\BackfillFromMobileCommons::class,
        'mobilecommons:status' => Commands\StatusCheck::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Queue a new "fetch" every 5 minutes. This will grab all profiles
        // that were updated in the last 10 minutes (overlap intended).
        $schedule->command('mobilecommons:fetch')->everyFiveMinutes();

        // Restart the queue daemons every hour to prevent memory issues.
        $schedule->command('queue:restart')->hourly();
    }
}
