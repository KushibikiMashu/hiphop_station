<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Alaouy\Youtube\Facades\Youtube;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use DateTime;

class GetVideoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getvd';

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
     * YouTube APIからchannelテーブルのハッシュを用いて動画を取得する
     *
     * @return mixed
     */
    public function handle()
    {
        $start = strtotime(Carbon::now());
        date_default_timezone_set("Asia/Tokyo");
        $channel_query = DB::table('channel')->select('*')->get();

        if(count($channel_query) === 1){
            echo "There is only one record in the channel table.\n";
            exit;
        }

        $now = Carbon::now();
        $now_timestamp = strtotime($now);
        $videos = [];
        foreach($channel_query as $record){
             // チャンネルの動画数が0、もしくはすでに動画を取得している場合はskip
            $video_exist = DB::table('video')->where('channel_id', '=', $record->id)->get();
            if($record->video_count === 0 || count($video_exist) > 1){
                continue;
            }
           $videos[] = $this->fetch_channel_data($record->channel_hash, $record->published_at, $now_timestamp);
        }

        if(count($videos) === 0){
            echo "There is no video in variable videos.\n";
            exit;
        }

        for($i = 0; $i < count($videos); $i++){
            for($j = 0; $j < count($videos[$i]); $j++){
                // videoテーブルにデータを挿入する
                $channel_id = DB::table('channel')->where('channel_hash', '=', $videos[$i][$j]->snippet->channelId)->first()->id;
                $hash = $videos[$i][$j]->id->videoId;
                $title = $videos[$i][$j]->snippet->title;
                $video_published_at = $videos[$i][$j]->snippet->publishedAt;

                DB::insert(
                    'insert into video (channel_id, title, video_hash, published_at, created_at, updated_at) 
                    values (?, ?, ?, ?, ?, ?)',
                    [ $channel_id, $title, $hash, $video_published_at, $now, $now]
                );

                // video_thumbnailsテーブルにデータを挿入する
                $video_id = DB::table('video')->where('video_hash', '=', $hash)->first()->id;
                $medium = $videos[$i][$j]->snippet->thumbnails->medium->url;
                $high = $videos[$i][$j]->snippet->thumbnails->high->url;

                DB::insert(
                    'insert into video_thumbnails (video_id, medium, high, created_at, updated_at) 
                    values (?, ?, ?, ?, ?)',
                    [$video_id, $medium, $high, $now, $now]
                );
            }
        }

        $end = strtotime(Carbon::now());
        echo $end - $start;
        echo "second\nend\n";

    }

    /**
     * チャンネル公開日から現在時刻まで、１週間ごとに動画を取得して配列$videosに格納する
     *
     * @return (array)videos
     */
    private function fetch_channel_data($channel_hash, $channel_published_at, $now_timestamp){
        $videos = [];
        $pub_date = new DateTime($channel_published_at);
        $pub_timestamp = strtotime($channel_published_at);

        while($pub_timestamp < $now_timestamp){
            $res = [];
            $after = Carbon::createFromTimestamp($pub_timestamp)->format(DateTime::ATOM);
            $after = substr($after, 0, 19) . '.000Z';
            $before = Carbon::createFromTimestamp($pub_timestamp)->addweek()->format(DateTime::ATOM);
            $before = substr($before, 0, 19) . '.000Z';

            // beforeが現在時刻を超えたら、現在時刻を利用する
            if($now_timestamp < strtotime($before)){
                $before_now = Carbon::createFromTimestamp($now_timestamp)->addweek()->format(DateTime::ATOM);
                $before = substr($before_now, 0, 19) . '.000Z';
            }

            // $resはfalseもしくはarray(1)
            $res = Youtube::listChannelVideos($channel_hash, 10, $after, $before);
            if(is_array($res)){
                $videos[] = $res[0];
            }

            // タイムスタンプを１週間分インクリメントする
            $pub_timestamp = strtotime($pub_date->modify('+1 weeks')->format('Y-m-d H:i:s')); 
        }

        return $videos;
    }
}
