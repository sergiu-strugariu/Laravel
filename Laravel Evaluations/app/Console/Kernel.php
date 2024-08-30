<?php

namespace App\Console;

use App\Console\Commands\AssessorRemindCustomPeriod;
use App\Console\Commands\AssessorRemindTaskUpdate;
use App\Console\Commands\ChangeToIssueOnTestNotDone;
use App\Console\Commands\GenerateRevenuePerDayStatistics;
use App\Console\Commands\GenerateRevenuePerLanguageStatistics;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\Currency;
use App\Console\Commands\TestTakeReminder;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Currency::class,
        TestTakeReminder::class,
        AssessorRemindTaskUpdate::class,
        ChangeToIssueOnTestNotDone::class,
        GenerateRevenuePerDayStatistics::class,
        GenerateRevenuePerLanguageStatistics::class,
        AssessorRemindCustomPeriod::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('reminder:test_take')->everyFiveMinutes();
        $schedule->command('reminder:assessor_task_update')->everyFiveMinutes();
        $schedule->command('update:status:issue')->everyFiveMinutes();
        $schedule->command('reminder:assessor:custom')->everyFiveMinutes();
        $schedule->command('statistics:revenue:day')->daily();
        $schedule->command('statistics:revenue:language')->everyFifteenMinutes();
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
