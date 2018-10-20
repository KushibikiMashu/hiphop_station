<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Alaouy\Youtube\Facades\Youtube;

class FetchLatestVideosFromYoutubeAPI extends Command
{
    const CHANNEL_TABLE = 'channel';
    const VIDEO_TABLE = 'video';
    const VIDEO_THUMBNAIL_TABLE = 'video_thumbnail';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:newVideo';

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
     * keyは動画のhash、valueは添字の連想配列
     *
     * @var array
     */
    private $flipped_video_hash;

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
        $this->set_flipped_video_hash();
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

        try {
            // Youtube APIで新着動画を取得する
            $after = $this->fetch_max_published_datetime();
            $before = substr($now->format(DateTime::ATOM), 0, 19) . '.000Z';
            $channel_data = $res = [];
            
            foreach($this->channel_query as $query) {
                $res = Youtube::listChannelVideos($query->hash, 30, $after, $before);
                if(!$res) continue;
                $channel_data[] = $res;
            }

            // 新着動画がなければ処理を終える
            if(empty($channel_data)) return;

            // videoテーブルに挿入する連想配列を取得
            foreach($channel_data as $channel_videos) {
                foreach($channel_videos as $channel_video){
                    // videoのhashが重複していればskipする
                    $hash = $channel_video->id->videoId;
                    if(isset($this->flipped_video_hash[$hash])) continue;
                    $video_records[] = $this->prepare_video_record($channel_video, $now);
                }
            }

            // 動画が全て重複していれば処理を終える
            if(empty($video_records)) return;
            DB::table(self::VIDEO_TABLE)->insert($video_records);

            // video_thumbnailsテーブルに挿入する連想配列を取得
            foreach($channel_data as $channel_videos) {
                foreach($channel_videos as $channel_video){
                    $hash = $channel_video->id->videoId;
                    if(isset($this->flipped_video_hash[$hash])) continue;
                    $video_thumbnail_records[] = $this->prepare_video_thumbnail_record($channel_video, $now);
                }
            }

            DB::table(self::VIDEO_THUMBNAIL_TABLE)->insert($video_thumbnail_records);

        } catch (Exception $e) {
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
        $max = ['id' => '', 'datetime' => Carbon::now()->subYear()->format('Y-m-d H:i:s')];
        foreach($this->video_query as $query) {
            $date = date('Y-m-d H:i:s', strtotime($query->published_at));
            if($max['datetime'] < $date){
                $max['id'] = $query->id;
                $max['datetime'] = $date;
            }
        }

        $max_datetime_query = DB::table(self::VIDEO_TABLE)
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
        $file = dirname(__FILE__) . '/../../../tests/json/res.json';
        $json = file_get_contents($file);
        return json_decode($json);
    }

    /**
     * videoテーブルに格納するレコードを作成する
     *
     * @param object $channel_video
     * @param datetime $now
     * @return array
     */
    private function prepare_video_record(object $channel_video, datetime $now): array
    {
        $channel_id = DB::table(self::CHANNEL_TABLE)->where('hash', '=', $channel_video->snippet->channelId)->first()->id;
        $title = $channel_video->snippet->title;
        $genre = $this->determine_video_genre($channel_id, $title);

        return [
            'channel_id' => $channel_id,
            'title' => $title,
            'hash' => $channel_video->id->videoId,
            'genre' => $genre,
            'published_at' => $channel_video->snippet->publishedAt,
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
     * @param array $channel_video
     * @param datetime $now
     * @return array
     */
    private function prepare_video_thumbnail_record(object $channel_video, datetime $now): array
    {
        return [
            'video_id' => DB::table(self::VIDEO_TABLE)->where('hash', '=', $channel_video->id->videoId)->first()->id,
            'std' => $channel_video->snippet->thumbnails->default->url,
            'medium' => $channel_video->snippet->thumbnails->medium->url,
            'high' => $channel_video->snippet->thumbnails->high->url,
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

    private function set_video_query()
    {
        $this->video_query = DB::table(self::VIDEO_TABLE)
            ->get();
    }

    private function set_channel_query()
    {
        $this->channel_query = DB::table(self::CHANNEL_TABLE)
            ->get();
    }

    /**
     * 動画のダブルチェックのためにhashを格納した配列を用意する
     * keyはhash、valueは添字
     */
    private function set_flipped_video_hash()
    {
        $video_hashes = [];
        foreach($this->video_query as $query){
            $video_hashes[] = $query->hash;
        }
        $this->flipped_video_hash = array_flip($video_hashes);
    }
}
