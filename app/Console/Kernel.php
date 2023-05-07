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
        $schedule->command(ResetLeaderboardCommand::class, ['--force'])->daily();
        $schedule->command(MeetingReminderCommand::class, ['--force'])
                 ->dailyAt('07:00')
                 ->timezone('Asia/Jakarta');
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
