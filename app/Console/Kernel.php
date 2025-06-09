<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\ProcessScheduledTerminations::class,
        \App\Console\Commands\UpdateWorkdaysFromHiredate::class,
        \App\Console\Commands\UpdateWorkedDaysCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Procesar bajas programadas cada día a las 00:01
        $schedule->command('app:process-scheduled-terminations')
                ->dailyAt('00:01')
                ->appendOutputTo(storage_path('logs/scheduled-terminations.log'));
        
        // Actualizar días trabajados de asalariados activos cada día a las 23:50
        $schedule->command('app:update-workdays-from-hiredate --only-active')
                ->dailyAt('23:50')
                ->appendOutputTo(storage_path('logs/workdays-update.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
