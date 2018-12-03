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
        //　新しいchannelを追加した時に叩くコマンド
        Commands\FetchNewChannel::class,
        Commands\NewVideoFetcherRelatedToNewChannel::class,

        // 全てのvideo, channelのサムネイルを取得するコマンド
        Commands\FetchVideoThumbnailImage::class,
        Commands\FetchChannelThumbnailImage::class,

        // ５分ごとに叩くコマンド
        Commands\FetchLatestVideosFromYoutubeApi::class,
        Commands\CreateJsonOfLatestVideoAndChannel::class,
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
