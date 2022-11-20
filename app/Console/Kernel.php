<?php

namespace App\Console;

use App\Models\Token;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        //delete from tokens table where created at time is after more than 2 minute deleted every 2 minutes
//          $schedule->call(function (){
//             Token::query()->whereDate('created_at','<=',Carbon::now()->subMinutes(2)->toDateTimeString())->delete();
//         })->everyTwoMinutes();
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
