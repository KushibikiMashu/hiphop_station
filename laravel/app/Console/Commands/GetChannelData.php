<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Alaouy\Youtube\Facades\Youtube;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class GetChannelData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getch';

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
        $start = strtotime(Carbon::now());
        date_default_timezone_set("Asia/Tokyo");

        // チャンネルIDを集めた配列（jsonファイルを読み込む）
        // $hashes = json_decode(外部ファイル)

        $hash = 'UCEc1YzMOSKKtJD7H-q71HgQ';
        // foreach($hashes as $hash){
            // チャンネルの情報を取得する
            $channel = Youtube::getChannelById('UCEc1YzMOSKKtJD7H-q71HgQ');

            // channelテーブルに挿入するデータ
            $title = $channel->snippet->title;
            $published_at = $channel->snippet->publishedAt;
            $video_count = $channel->statistics->videoCount;
            $time = Carbon::now();

            DB::insert(
                'insert into channel (title, channel_hash, published_at, video_count, created_at, updated_at) 
                values (?, ?, ?, ?, ?, ?)',
                [$title, $hash, $published_at, (int)$video_count, $time, $time]
            );

            // channel_thumbnailsテーブルに挿入するデータ
            $channel_id = DB::table('channel')->where('channel_hash', '=', $hash)->first()->id;
            $medium = $channel->snippet->thumbnails->medium->url;
            $high = $channel->snippet->thumbnails->high->url;
            
            DB::insert(
                'insert into channel_thumbnails (channel_id, medium, high, created_at, updated_at) 
                values (?, ?, ?, ?, ?)',
                [$channel_id, $medium, $high, $time, $time]
            );
        // }
    }
}
