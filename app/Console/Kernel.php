<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    protected $commands = [
        Commands\CheckActionStatuses::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Check action statuses hourly at the start of the hour
        $schedule->command('actions:check-statuses')
            ->hourly()
            ->withoutOverlapping();

        // Process scheduled notifications hourly, 5 minutes after the hour
        $schedule->command('notifications:process')
            ->hourlyAt(5) // Runs at :05 of every hour
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
