<?php

namespace App\Repositories;

use Illuminate\Support\Carbon;
use App\Services\CustomizedYoutubeApi;

class ApiRepository implements ApiRepositoryInterface
{
    private $video_repo;
    private $channel_repo;
    private $youtube;
    private $extended_youtube;

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
        ChannelRepository $channel_repo,
        \Youtube $youtube,
        CustomizedYoutubeApi $extended_youtube
    )
    {
        $this->video_repo = $video_repo;
        $this->channel_repo = $channel_repo;
        $this->youtube = $youtube;
        $this->extended_youtube = $extended_youtube;
    }

    public function getNewVideosOfRegisteredChannel(): array
    {
        $now = Carbon::now();
        $after = $this->video_repo->fetchLatestPublishedAtVideoRecord()->published_at;
        $before = substr($now->format(\DateTime::ATOM), 0, 19) . '.000Z';
        $query = $this->channel_repo->fetchAnyColumn('hash');
        $videos = $res = [];

        foreach ($query as $hashes) {
            foreach ($hashes as $hash) {
                $res = $this->extended_youtube->listChannelVideos($hash, 50, $after, $before);
                // 新しいvideoがない場合(false)か、基準になる日付の動画は配列$videosに追加しない
                if ($res === false || (count($res) === 1 && $res[0]->snippet->publishedAt === $after)) {
                    continue;
                }
                $videos[] = $res;
            }
        }
        return $videos;
    }

    /**
     * channelのhash（YouTubeの表記はChannelId）からchannelのデータを取得する
     *
     * @param $hash
     * @return array
     * @throws \Exception
     */
    public function getChannelByHash($hash): array
    {
        $res = $this->youtube::getChannelById($hash);
        $channel = [
            'title'        => $res->snippet->title,
            'hash'         => $hash,
            'video_count'  => $res->statistics->videoCount,
            'published_at' => $res->snippet->publishedAt,
        ];

        $channel_thumbnail = [
            'std'    => $res->snippet->thumbnails->default->url,
            'medium' => $res->snippet->thumbnails->medium->url,
            'high'   => $res->snippet->thumbnails->high->url,
        ];
        return [$channel, $channel_thumbnail];
    }

    /**
     * 指定した期間の動画を取得する
     *
     * @param int $channel_id
     * @param string $channel_hash
     * @param int $maxResult
     * @param string $after
     * @param string $before
     * @return array
     * @throws \Exception
     */
    public function getNewVideosByChannelHash(int $channel_id, string $channel_hash, int $maxResult, string $after, string $before): array
    {
        $res = $this->extended_youtube->listChannelVideos($channel_hash, $maxResult, $after, $before);
        if ($res === false) return [null, null];

        $videos = $video_thumbnails = [];
        foreach ($res as $data) {
            $title = $data->snippet->title;
            $genre = $this->determine_video_genre($channel_id, $title);

            $videos[] = [
                'channel_id'   => $channel_id,
                'title'        => $title,
                'hash'         => $data->id->videoId,
                'genre'        => $genre,
                'published_at' => $data->snippet->publishedAt,
            ];

            if ($data->snippet->liveBroadcastContent === 'none') {
                $video_thumbnails[] = [
                    'std'    => $data->snippet->thumbnails->default->url,
                    'medium' => $data->snippet->thumbnails->medium->url,
                    'high'   => $data->snippet->thumbnails->high->url,
                ];
            } else {
                $video_thumbnails[] = [
                    'std'    => str_replace('_live', '', $data->snippet->thumbnails->default->url),
                    'medium' => str_replace('_live', '', $data->snippet->thumbnails->medium->url),
                    'high'   => str_replace('_live', '', $data->snippet->thumbnails->high->url),
                ];
            }
        }
        return [$videos, $video_thumbnails];
    }

    /**
     * 試着動画のgenreを振り分ける
     *
     * @param int $channel_id
     * @param string $title
     * @return string
     */
    public function determine_video_genre(int $channel_id, string $title): string
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
            case 2:
                $genre = 'interview';
                break;
            // case 3:
            //     $genre = 'radio';
            //     break;
            default:
                break;
        }
        return $genre;
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
    private
    function array_strpos(string $haystack, array $needles, int $offset = 0): bool
    {
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle, $offset) !== false) {
                return true;
            }
        }
        return false;
    }

}
