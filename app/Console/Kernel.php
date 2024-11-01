<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
///usr/local/bin/php /home/tgcrmc5/development.tgcrm.net/artisan schedule:run > /dev/null 2>&1
///
/// php artisan password:send-reset-emails
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        /** Temporary Disabled*/
//        $schedule->command('verify:phone')
//            ->everyMinute();
//        $schedule->command('verify:ip')
//            ->everyMinute();
//        $schedule->command('remaining:transaction')
//            ->everyMinute();
        $schedule->command('command:RefreshEmailConfigurationAccessToken')->everyThirtyMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
