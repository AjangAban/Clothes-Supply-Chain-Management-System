<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SendSupplierReports;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('ml:export-data')->daily();
        $schedule->exec('python ' . base_path('ml_recommendation.py'))->daily()->after('ml:export-data');
        $schedule->command('send:supplier-reports')->daily();
        $schedule->command('send:delivery-partner-reports')->daily();
        $schedule->command('send:warehouse-manager-reports')->daily();
        $schedule->command('send:manufacturer-reports')->daily();
        
                     

    }
    
    

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    protected $commands = [
        SendSupplierReports::class,
    ];


}
