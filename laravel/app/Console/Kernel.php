<?php

namespace App\Console;

use App\Console\Commands\CreateJsonOfLatestVideoAndChannel;
use App\Console\Commands\Services\ThumbnailImageFetcher;
use App\ChannelThumbnail;
use App\VideoThumbnail;
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
        Commands\FetchVideoThumbnailImage::class,
        Commands\FetchChannelThumbnailImage::class,
        Commands\FetchLatestVideosFromYoutubeAPI::class,
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
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('fetch:video')
            ->everyMinute()
            ->after(function () {
                (new ThumbnailImageFetcher(new ChannelThumbnail))->downloadImages();
                (new ThumbnailImageFetcher(new VideoThumbnail))->downloadImages();
                (new CreateJsonOfLatestVideoAndChannel)->handle();
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
