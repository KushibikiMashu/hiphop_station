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
        Commands\GetChannelData::class,
        Commands\GetVideoData::class,
        Commands\GenerateJson::class,
        Commands\FetchVideoThumbnailImage::class,
        Commands\FetchChannelThumbnailImage::class,
        Commands\FetchLatestVideosFromYoutubeApi::class,
        Commands\UpdateAddressOfStdVideoThumbnail::class,
        Commands\CreateJsonOfLatestVideoAndChannel::class,
        Commands\CreateResponseJsonFromYoutubeAPIForTest::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule)
    {
        $schedule->command('fetch:video')
            ->everyFiveMinutes()
            ->after(function () {
                $this->call('create:json');
            })
            ->timezone('Asia/Tokyo');
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
