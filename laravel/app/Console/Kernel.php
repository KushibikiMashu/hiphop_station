<?php

namespace App\Console;

use App\Console\Commands\CreateJsonOfLatestVideoAndChannel;
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
        Commands\GetChannelData::class,
        Commands\GetVideoData::class,
        Commands\GenerateJson::class,
        Commands\ExcuteUpdateVideoGenreQuery::class,
        Commands\FetchLatestVideosFromYoutubeAPI::class,
        Commands\UpdateAddressOfStdVideoThumbnail::class,
        Commands\CreateJsonOfLatestVideoAndChannel::class,
        Commands\CreateResponseJsonFromYoutubeAPIForTest::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('fetch:video')
            ->everyMinute()
            ->after(function () {
                $create_json = new CreateJsonOfLatestVideoAndChannel;
                $create_json->handle();
            })
            ->timezone('Asia/Tokyo')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
