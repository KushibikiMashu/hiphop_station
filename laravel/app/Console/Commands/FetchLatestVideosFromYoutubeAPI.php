<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\CustomizedYoutubeApi;
use App\Services\FetchLatestVideosFromYoutubeApiService;

class FetchLatestVideosFromYoutubeApi extends Command
{
    /**
     * genreをbattle, songに振り分けるための動画タイトルのキーワード
     */
    const words = [
        '2' => ['KOK', 'KING OF KINGS', 'SCHOOL OF RAP'],
        '23' => ['SPOTLIGHT', 'ENTER'],
        'song' => ['【MV】', 'Music Video', 'MusicVideo'],
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:video';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest videos';

    // channelテーブルの全レコード
    private $channel_query;
    // videoテーブルの全レコード
    private $video_query;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->video_query = DB::table(config('const.TABLE.VIDEO'))->get();
        $this->channel_query = DB::table(config('const.TABLE.CHANNEL'))->get();
    }

    /**
     * YoutubeAPIから最新の動画を取得する
     * (設計方針：一つの関数に一つの関心事。オブジェクト指向)
     *
     * @param FetchLatestVideosFromYoutubeApiService $service
     */
    public function handle(FetchLatestVideosFromYoutubeApiService $service)
    {
//        $service->run();



        date_default_timezone_set("Asia/Tokyo");
        $now = Carbon::now();

        try {
            // Youtube APIで新着動画を取得する
            $after = $this->fetch_max_published_datetime();
            $before = substr($now->format(DateTime::ATOM), 0, 19) . '.000Z';
            $channel_data = $res = [];

            foreach ($this->channel_query as $query) {
                $res = $youtube->listChannelVideos($query->hash, 30, $after, $before);
                if (!$res) {
                    continue;
                }
                $channel_data[] = $res;
            }

            // keyはvideoのhash、valueは添字
            $flipped_video_hash = $this->video_query->pluck('hash')->flip();

            // videoテーブルに挿入する連想配列を取得
            foreach ($channel_data as $channel_videos) {
                foreach ($channel_videos as $channel_video) {
                    // videoのhashが重複していればskipする
                    if (isset($flipped_video_hash[$channel_video->id->videoId])) {
                        continue;
                    }
                    $video_records[] = $this->prepare_video_record($channel_video, $now);
                }
            }

            // 動画が全て重複していれば処理を終える
            if (empty($video_records)) {
                $this->info("[{$now}] No new video fetched");
                Log::info('No new video fetched');
                return;
            } else {
                $this->info("[{$now}] The number of fetched video: " . (string)count($channel_data));
                $this->info("[{$now}] The title of latest video is " . $channel_data[0][0]->snippet->title);
                Log::info('The number of fetched video: ' . (string)count($channel_data));
                Log::info('The title of latest video is ' . $channel_data[0][0]->snippet->title);
            }

            DB::table(config('const.TABLE.VIDEO'))->insert($video_records);

            // video_thumbnailsテーブルに挿入する連想配列を取得
            foreach ($channel_data as $channel_videos) {
                foreach ($channel_videos as $channel_video) {
                    if (isset($flipped_video_hash[$channel_video->id->videoId])) {
                        continue;
                    }

                    $video_thumbnail_records[] = $this->prepare_video_thumbnail_record($channel_video, $now);
                }
            }

            DB::table(config('const.TABLE.VIDEO_THUMBNAIL'))->insert($video_thumbnail_records);

        } catch (Exception $e) {
            report($now);
            report('Failed to fetch latest videos.');
            return;
        }
    }

    /**
     * videoテーブルのレコードの最新の日付を取得する
     *
     * @return string
     */
    public function fetch_max_published_datetime(): string
    {
        $max = ['id' => '', 'datetime' => Carbon::now()->subYear()->format('Y-m-d H:i:s')];
        foreach ($this->video_query as $query) {
            $date = date('Y-m-d H:i:s', strtotime($query->published_at));
            if ($max['datetime'] < $date) {
                $max['id'] = $query->id;
                $max['datetime'] = $date;
            }
        }

        $max_datetime_query = DB::table(config('const.TABLE.VIDEO'))
            ->select('published_at')
            ->where('id', '=', $max['id'])
            ->get();

        return $max_datetime_query[0]->published_at;
    }

    /**
     * videoテーブルに格納するレコードを作成する
     *
     * @param object $channel_video
     * @param datetime $now
     * @return array
     */
    private function prepare_video_record($channel_video, datetime $now): array
    {
        $channel_id = DB::table(config('const.TABLE.CHANNEL'))->where('hash', '=', $channel_video->snippet->channelId)->first()->id;
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
        switch ($channel_id) {
            // 基本的にsong
            case '2':
                // 配列はプロパティで持つ
                if ($this->array_strpos($title, self::words['2']) === true) {
                    $flag = 1;
                }
                break;
            // 基本的にbattle
            case '8':
                $flag = 1;
                if ($this->array_strpos($title, self::words['song']) === true) {
                    $flag = 0;
                }
                break;
            // 基本的にbattle
            case '9':
                $flag = 1;
                if ($this->array_strpos($title, self::words['song']) === true) {
                    $flag = 0;
                }
                break;
            // 基本的にsong
            case '23':
                if ($this->array_strpos($title, self::words['23']) === true) {
                    $flag = 1;
                }
                break;
            default:
                break;
        }

        switch ($flag) {
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
            // case 3:
            //     $genre = 'radio';
            //     break;
            default:
                break;
        }
        return $genre;
    }

    /**
     * video_thumbnailテーブルに格納するレコードを作成する
     *
     * @param object $channel_video
     * @param datetime $now
     * @return array
     */
    private function prepare_video_thumbnail_record($channel_video, datetime $now): array
    {
        return [
            'video_id' => DB::table(config('const.TABLE.VIDEO'))->where('hash', '=', $channel_video->id->videoId)->first()->id,
            'std' => str_replace('_live', '', $channel_video->snippet->thumbnails->default->url),
            'medium' => str_replace('_live', '', $channel_video->snippet->thumbnails->medium->url),
            'high' => str_replace('_live', '', $channel_video->snippet->thumbnails->high->url),
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
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle, $offset) !== false) {
                return true;
            }
        }
        return false;
    }
}
