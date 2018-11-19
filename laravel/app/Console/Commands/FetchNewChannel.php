<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FetchNewChannelService;

class FetchNewChannel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:newChannel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @param FetchNewChannelService $service
     * @throws \Exception
     */
    public function handle(FetchNewChannelService $service): void
    {
        // channelを集めたjsonファイルから取り出したハッシュでAPIを叩く
        $channels = $this->getChannelJson();
        $result = $service->run($channels);
        $result ? \Log::info('Complete inserting new channels and channel thumbnails') : \Log::info('No new channel');
    }

    private function getChannelJson(): array
    {
        $json = file_get_contents(dirname(__FILE__) . '/youtube_channel.json');
        return json_decode($json, true);
    }
}
