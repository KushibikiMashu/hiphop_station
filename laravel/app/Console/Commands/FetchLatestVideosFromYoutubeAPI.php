<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Alaouy\Youtube\Facades\Youtube;
use Exception;

class FetchLatestVideosFromYoutubeAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'FetchLatestVideosFromYoutubeAPI';

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
        $video_table_name = 'video_copy';
        $video_thumbnail_table_name = 'video_thumbnail_copy';

        date_default_timezone_set("Asia/Tokyo");
        $now = Carbon::now();

        $res = $this->read_API_response_file();

        try {
            $after = $this->fetch_max_published_datetime();
            $before = substr($now->format(DateTime::ATOM), 0, 19) . '.000Z';

            // TODO Youtube APIでresponseを取得する

            // videoテーブルに挿入するデータを取得
            for($i = 0; $i < count($res); $i++){
                $video_records[] = $this->prepare_video_record($i, $res, $now);
            }

            // テスト終了後、video_copyをvideoに変更
            DB::table($video_table_name)->insert($video_records);

            // video_thumbnailsテーブルに挿入するデータを取得
            for($i = 0; $i < count($res); $i++){
                $video_thumbnail_records[] = $this->prepare_video_thumbnail_record($i, $res, $now);
            }

            // テスト終了後、video_copyをvideoに変更
            DB::table($video_thumbnail_table_name)->insert($video_thumbnail_records);
            
        } catch (Exception $e) {
            report($e);
            report($now);
            report('Failed to fetch latest videos.');

            return false;
        }
    }

    /**
     * videoテーブルのレコードの最新の日付を取得する
     *
     * @return string
     */
    private function fetch_max_published_datetime(): string
    {
        $max = ['id' => '', 'datetime' => '2000-01-01 00:00:00'];
        foreach($this->video_query as $query) {
            $date = date('Y-m-d H:i:s', strtotime($query->published_at));
            if($max['datetime'] < $date){
                $max['id'] = $query->id;
                $max['datetime'] = $date;
            }
        }

        $max_datetime_query = DB::table('video')
                            ->select('published_at')
                            ->where('id', '=', $max['id'])
                            ->get();

        return  $max_datetime_query[0]->published_at;
    }

    /**
     * （テスト用）Youtube APIで返却される配列を返却
     *
     * @return array
     */
    private function read_API_response_file(): array
    {
        $file = dirname(__FILE__) . '/../../../tests/json/response.json';
        $json = file_get_contents($file);
        return json_decode($json);
    }

    /**
     * videoテーブルに格納するレコードを作成する
     *
     * @param integer $i
     * @param array $res
     * @param datetime $now
     * @return array
     */
    private function prepare_video_record(int $i, array $res, datetime $now): array
    {
        $channel_id = DB::table('channel')->where('hash', '=', $res[$i]->snippet->channelId)->first()->id;
        $title = $res[$i]->snippet->title;
        $genre = $this->determine_video_genre($channel_id, $title);

        return [
            'channel_id' => $channel_id,
            'title' => $title,
            'hash' => $res[$i]->id->videoId,
            'genre' => $genre,
            'published_at' => $res[$i]->snippet->publishedAt,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function determine_video_genre(int $channel_id, string $title): string
    {
        /** 
         * titleとchannel_idでgenreを分類する
         * shinjuku tokyo, UMB, 戦国MCBattle, ifktv
         * $flagで状態を持つ。0はsong。1はbattle。今後2はinterviewの予定
         */
        $flag = 0;
        switch($channel_id) {
            // 基本的にsong
            case '2':
                // 配列はプロパティで持つ
                if($this->array_strpos($title, ['KOK', 'KING OF KINGS', 'SCHOOL OF RAP']) === true){
                    $flag = 1;
                }
                break;
            // 基本的にbattle
            case '8':
                $flag = 1;
                if($this->array_strpos($title, ['【MV】', 'Music Video', 'MusicVideo']) === true){
                    $flag = 0;
                }
                break;
            // 基本的にbattle            
            case '9':
                $flag = 1;
                if($this->array_strpos($title, ['【MV】', 'Music Video', 'MusicVideo']) === true){
                    $flag = 0;
                }
                break;
            // 基本的にsong
            case '23':
                if($this->array_strpos($title, ['SPOTLIGHT', 'ENTER']) === true){
                    $flag = 1;
                }
                break;
            default:
                break;
        }

        switch($flag) {
            case 0:
                $genre = 'song';
                break;
            case 1:
                $genre = 'battle';
                break;
            // TODO 追加予定。プログラムの拡張性を考えて
            // case 2:
            //     $genre = 'interview';
            //     break;
            default:
                break;
        }

        return $genre;
    }

    /**
     * video_thumbnailテーブルに格納するレコードを作成する
     *
     * @param integer $i
     * @param array $res
     * @param datetime $now
     * @return array
     */
    private function prepare_video_thumbnail_record(int $i, array $res, datetime $now): array
    {

    $video_table_name = 'video_copy';

        return [
            'video_id' => DB::table($video_table_name)->where('hash', '=', $res[$i]->id->videoId)->first()->id,
            'std' => $res[$i]->snippet->thumbnails->default->url,
            'medium' => $res[$i]->snippet->thumbnails->medium->url,
            'high' => $res[$i]->snippet->thumbnails->high->url,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    /**
     * $needleを配列にしたstrposの文字列検索をする
     *
     * @param string $haystack
     * @param array $needles
     * @param integer $offset
     * @return boolean
     */
    private function array_strpos(string $haystack, array $needles, int $offset = 0): bool
    {
        // $haystackの中に$needlesがあれば、trueを返す
        foreach($needles as $needle) {
            if(strpos($haystack, $needle, $offset) !== false) {
                return true;
            }
        }
        return false;
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
