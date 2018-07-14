<?php namespace App\Console;

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
        'App\Console\Commands\ScheduledPostCommand',
        'App\Console\Commands\GenerateVisualPostImagesCommand',
        'App\Console\Commands\TestCommand',
        'App\Console\Commands\PerformFollowRequestsCommand',
        'App\Console\Commands\PostNewlyLovedTracksCommand',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('tweekly:scheduled:post')
                 ->hourly()
                 ->sendOutputTo(storage_path('cron_posts.log'));

        $schedule->command('tweekly:scheduled:lastfm-loved')
                 ->everyTenMinutes()
                 ->sendOutputTo(storage_path('cron_loved.log'));
    }
}
