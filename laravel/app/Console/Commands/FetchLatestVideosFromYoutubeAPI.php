<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Services\FetchLatestVideosFromYoutubeApiService;

class FetchLatestVideosFromYoutubeApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:video';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest videos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * YoutubeAPIから最新の動画を取得する
     * (設計方針：一つの関数に一つの関心事。オブジェクト指向)
     *
     * @param FetchLatestVideosFromYoutubeApiService $service
     */
    public function handle(FetchLatestVideosFromYoutubeApiService $service)
    {
        $now = Carbon::now();
        $responses = $service->run();
        $responses ? $this->outputVideoLogs($now, $responses) : $this->outputNoVideoLog($now);
    }

    /**
     * 新着動画がない場合にログを出力する
     *
     * @param Carbon $now
     */
    private function outputNoVideoLog(Carbon $now): void
    {
        $message = 'No new video';
        $this->info("[{$now}] " . $message);
        \Log::info($message);
    }

    /**
     * 新着動画がある場合にログを出力する
     *
     * @param Carbon $now
     * @param array $responses
     */
    private function outputVideoLogs(Carbon $now, array $responses): void
    {
        // 動画が全て重複していれば処理を終える
        $this->info("[{$now}] The number of fetched video: " . (string)count(array_collapse($responses)));
        $this->info("[{$now}] The title of latest video is " . $responses[0][0]->snippet->title);
        \Log::info('The number of fetched video: ' . (string)count($responses));
        \Log::info('The title of latest video is ' . $responses[0][0]->snippet->title);
    }
}
