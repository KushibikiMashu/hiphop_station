<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use App\Repositories\VideoThumbnailRepository;
use App\Repositories\ChannelRepository;
use App\Repositories\ApiRepository;

class FetchLatestVideosFromYoutubeApiService
{
    private $video_repo;
    private $video_thumbnail_repo;
    private $channel_repo;
    private $api_repo;

    /**
     * genreをbattle, songに振り分けるための動画タイトルのキーワード
     */
    const words = [
        '2'    => ['KOK', 'KING OF KINGS', 'SCHOOL OF RAP'],
        '23'   => ['SPOTLIGHT', 'ENTER'],
        'song' => ['【MV】', 'Music Video', 'MusicVideo'],
    ];

    public function __construct(
        VideoRepository $video_repo,
        VideoThumbnailRepository $video_thumbnail_repo,
        ChannelRepository $channel_repo,
        ApiRepository $api_repo
    )
    {
        $this->video_repo = $video_repo;
        $this->video_thumbnail_repo = $video_thumbnail_repo;
        $this->channel_repo = $channel_repo;
        $this->api_repo = $api_repo;
    }

    public function run(): array
    {
        $responses = $this->api_repo->getNewVideosOfRegisteredChannel();
        // 新着動画がない場合は処理を終える
        if (empty($responses)) {
            return $responses;
        }

        $this->saveVideosAndThumbnails($responses);
        return $responses;
    }

    /**
     * 新着動画とサムネイルのデータをvideo, video_thumbnailテーブルに登録する
     *
     * @param array $responses
     */
    private function saveVideosAndThumbnails(array $responses): void
    {
        $registered_video_hashes = $this->video_repo->fetchPluckedColumn('hash')->flip();
        foreach ($responses as $videos) {
            foreach ($videos as $video) {
                // videoのhashが重複していればskipする
                if (isset($registered_video_hashes[$video->id->videoId])) {
                    continue;
                }
                $this->video_repo->saveRecord($this->prepare_video_record($video));
                $this->video_thumbnail_repo->saveRecord($this->prepare_video_thumbnail_record($video));
            }
        }
    }

    /**
     * videoテーブルに格納するレコードを作成する
     *
     * @param object $video
     * @return array
     */
    private function prepare_video_record($video): array
    {
        $channel_id = $this->channel_repo->fetchChannelIdByHash($video->snippet->channelId);
        $title = $video->snippet->title;
        $genre = $this->determine_video_genre($channel_id, $title);

        return [
            'channel_id'   => $channel_id,
            'title'        => $title,
            'hash'         => $video->id->videoId,
            'genre'        => $genre,
            'published_at' => $video->snippet->publishedAt
        ];
    }

    /**
     * 試着動画のgenreを振り分ける
     *
     * @param int $channel_id
     * @param string $title
     * @return string
     */
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
     * @param object $video
     * @return array
     */
    private function prepare_video_thumbnail_record($video): array
    {
        return [
            'video_id' => $this->video_repo->fetchVideoIdByHash($video->id->videoId),
            'std'      => str_replace('_live', '', $video->snippet->thumbnails->default->url),
            'medium'   => str_replace('_live', '', $video->snippet->thumbnails->medium->url),
            'high'     => str_replace('_live', '', $video->snippet->thumbnails->high->url),
        ];
    }

    /**
     * $needleを配列にしたstrposの文字列検索をする
     * $haystackの中に$needlesがあればtrueを返す
     *
     * @param string $haystack
     * @param array $needles
     * @param integer $offset
     * @return boolean
     */
    private function array_strpos(string $haystack, array $needles, int $offset = 0): bool
    {
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle, $offset) !== false) {
                return true;
            }
        }
        return false;
    }
}
