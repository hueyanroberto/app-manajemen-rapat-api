<?php

namespace App\Console;

use App\Console\Commands\MeetingReminderCommand;
use App\Console\Commands\ResetLeaderboardCommand;
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
        $schedule->command('command:resetLeaderboard')
                 ->dailyAt('16:50')
                 ->timezone('Asia/Jakarta');
                 
        $schedule->command('command:MeetingReminder')
                 ->dailyAt('07:00')
                 ->timezone('Asia/Jakarta');

        $schedule->command('cron:log')
                 ->everyMinute();
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
