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
        $video = DB::table('video')->get();

        $songs = [];
        $i = 0;
        foreach($video as $song){
            $songs[$i]['video_hash'] = $song->video_hash;
            $songs[$i]['title'] = $song->title;
            $songs[$i]['date']  = $song->published_at;
            $i++;
        }

        $json = json_encode($songs, JSON_UNESCAPED_UNICODE);
        var_dump($json);
    }
}
