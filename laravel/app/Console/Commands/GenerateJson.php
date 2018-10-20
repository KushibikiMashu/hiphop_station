<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'genjson';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $videos = DB::table('video')
            ->orderBy('published_at', 'desc')
            ->get();
        $channels = DB::table('channel')
            ->get();

        $songs = [];
        $i = 0;

        foreach ($videos as $video) {
            $songs[$i]['hash'] = $video->video_hash;
            $songs[$i]['title'] = $video->title;
            $songs[$i]['date'] = $video->published_at;
            $songs[$i]['channel'] = $channels[$video->channel_id - 1]->title;
            $songs[$i]['img'] = 'https://i.ytimg.com/vi/' . $video->video_hash . '/hqdefault.jpg';
            $i++;
        }

        $json = json_encode($songs, JSON_UNESCAPED_UNICODE);
        $file = dirname(__FILE__) . '/../../../public/json/songs.json';
        file_put_contents($file, $json);
    }
}
