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
     */
    public function handle(FetchNewChannelService $service)
    {
        // channelを集めたjsonファイルから取り出したハッシュでAPIを叩く
        $array = json_decode(file_get_contents(dirname(__FILE__) . '/youtube_channel.json'), true);
        $service->run($array);
        // log出力
    }
}
