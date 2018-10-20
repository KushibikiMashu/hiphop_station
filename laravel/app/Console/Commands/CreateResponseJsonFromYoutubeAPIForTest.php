<?php

namespace App\Console\Commands;

use Alaouy\Youtube\Facades\Youtube;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CreateResponseJsonFromYoutubeAPIForTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CreateResponseJsonFromYoutubeAPIForTest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * channelテーブルの全レコード
     *
     * @var array
     */
    private $channel_query;

    /**
     * videoテーブルの全レコード
     *
     * @var array
     */
    private $video_query;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->set_video_query();
        $this->set_channel_query();
    }

    /**
     * YoutubeAPIから最新の動画を取得する
     * （設計方針：一つの関数に一つの関心事。オブジェクト指向）
     *
     * @return mixed
     */
    public function handle()
    {
        date_default_timezone_set("Asia/Tokyo");
        $now = Carbon::now();

        // 最新の動画を取得
        $after = $this->fetch_max_published_datetime();
        $before = substr($now->format(DateTime::ATOM), 0, 19) . '.000Z';
        $res = Youtube::listChannelVideos('UCzHhZATibT1t6iYEzDbbIaw', 50, $after, $before);

        if ($res === false) {
            dd('No new video');
        }

        // testフォルダにJSONを出力
        $json = json_encode($res, JSON_UNESCAPED_UNICODE);
        $file = dirname(__FILE__) . '/../../../tests/json/add_response.json';
        file_put_contents($file, $json);
    }

    /**
     * videoテーブルのレコードの最新の日付を取得する
     *
     * @return string
     */
    private function fetch_max_published_datetime()
    {
        $max = ['id' => '', 'datetime' => '2000-01-01 00:00:00'];
        foreach ($this->video_query as $query) {
            $date = date('Y-m-d H:i:s', strtotime($query->published_at));
            if ($max['datetime'] < $date) {
                $max['id'] = $query->id;
                $max['datetime'] = $date;
            }
        }

        $max_datetime_query = DB::table('video')
            ->select('published_at')
            ->where('id', '=', $max['id'])
            ->get();
        $max_datetime = $max_datetime_query[0]->published_at;

        return $max_datetime;
    }

    protected function set_video_query()
    {
        $this->video_query = DB::table('video')
            ->get();
    }

    protected function set_channel_query()
    {
        $this->channel_query = DB::table('channel')
            ->get();
    }
}
