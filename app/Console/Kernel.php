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
        Commands\Housekeep::class,
        Commands\Hourly::class,
        Commands\Quarters::class,
        Commands\Fixit::class,

        Commands\SeedDemo::class,
        Commands\MigrateDev::class,
        Commands\GenerateAppSuite::class
    ];


    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule){

        $filePath = base_path().'/storage/logs/housekeeplog-'.date('m-d-Y-H:i',time()).'.txt';
        $filePathHourly = base_path().'/storage/logs/hourlylog-'.date('m-d-Y-H:i',time()).'.txt';

        $schedule->command('sysadmin:housekeep')->daily()->at('02:00')
            ->timezone('America/Denver')
            ->sendOutputTo($filePath)
            ->emailOutputTo(config('app.support'));

        $schedule->command('sysadmin:hourly')->hourly()
            ->timezone('America/Denver')
            ->sendOutputTo($filePathHourly);
            
        $schedule->command('sysadmin:quarters')->hourly()
            ->timezone('America/Denver')
            ->sendOutputTo($filePathHourly);


        $schedule->command('backup:run')->daily()->at('03:00')
            ->timezone('America/Denver');
        $schedule->command('backup:clean')->daily()->at('04:00')
            ->timezone('America/Denver');
        $schedule->command('backup:monitor')->daily()->at('04:30')
            ->timezone('America/Denver');


        $schedule->command('grow:snapshot')->daily()->at('23:45')
            ->timezone('America/Denver');

    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(){

        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }

}
