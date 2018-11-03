<?php

namespace App\Console\Commands;

use App\Video;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:test';

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
        dump($this->testFlipVideoHashAndId());
    }

    private function testFlipVideoHashAndId()
    {
        // コレクションの関数を使用
        $flipped_array = Video::get()->pluck('hash')->flip();

        // FetchLatestVideosFromYoutubeAPIクラスのset_flipped_video_hash関数
        $video_query = DB::table(config('const.TABLE.VIDEO'))->get();
        $video_hashes = [];
        foreach ($video_query as $query) {
            $video_hashes[] = $query->hash;
        }
        $flipped_video_hash = array_flip($video_hashes);

        // $flipped_video_hashはid順だが、$flipped_arrayの順番がid通りではないため
        // return false
        return ($flipped_array === $flipped_video_hash);
    }
}
