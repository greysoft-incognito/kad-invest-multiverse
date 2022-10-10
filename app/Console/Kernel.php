<?php

namespace App\Console;

use App\Console\Commands\ExportFormData;
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
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command(ExportFormData::class)
        ->weekly()
        ->mondays()
        ->at('00:00');

        // $schedule->command(ExportFormData::class)
        // ->everyMinute();

        $schedule->command(ExportFormData::class)
            ->weekly()
            ->fridays()
            ->at('12:00');
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
