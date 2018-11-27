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

    public function __construct()
    {
        $this->video_repo       = new VideoRepository;
        $this->channel_repo     = new ChannelRepository;
        $this->youtube          = new \Youtube;
        $this->extended_youtube = new CustomizedYoutubeApi;
    }

    public function getNewVideosOfRegisteredChannel(): array
    {
        $now    = Carbon::now();
        $after  = $this->video_repo->fetchLatestPublishedAtVideoRecord()->published_at;
        $before = substr($now->format(\DateTime::ATOM), 0, 19) . '.000Z';
        $query  = $this->channel_repo->fetchAnyColumn('hash');
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
        $res     = $this->youtube::getChannelById($hash);
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

        return $this->processResponse($channel_id, $res);
    }

    public function getNewVideosByChannelHashUnderFiftyVideos(int $channel_id, string $channel_hash, int $maxResult): array
    {
        $res = $this->extended_youtube->listChannelVideos($channel_hash, $maxResult);
        if ($res === false) return [null, null];
        return $this->processResponse($channel_id, $res);
    }

    private function processResponse($channel_id, $res): array
    {
        $videos                  = $video_thumbnails = [];
        $registered_video_hashes = $this->video_repo->fetchPluckedColumn('hash')->flip();
        foreach ($res as $data) {
            if (isset($registered_video_hashes[$data->id->videoId])) continue;
            $title    = $data->snippet->title;
            $hash = $data->id->videoId;
            $genre    = $this->determine_video_genre($hash, $title);
            $videos[] = [
                'channel_id'   => $channel_id,
                'title'        => $title,
                'hash'         => $hash,
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
            \Log::info($title);
        }
        return [$videos, $video_thumbnails];
    }

    /**
     * 試着動画のgenreを振り分ける
     *
     * @param string $hash
     * @param string $title
     * @return string
     */
    public function determine_video_genre(string $hash, string $title): string
    {
        /**
         * titleとchannel_idでgenreを分類する
         * shinjuku tokyo, UMB, 戦国MCBattle, ifktv
         * $flagで状態を持つ。0はsong。1はbattle。今後2はinterviewの予定
         */
        $channels = config('channels');
        $keywords = config('const.KEYWORDS');
        $flag = 0;
        switch ($hash) {
            case $channels[1]['hash']:
                if ($this->array_strpos($title, $keywords['others']) === true) {
                    $flag = 3;
                }
                break;
            case $channels[2]['hash']:
                if ($this->array_strpos($title, $keywords['2']) === true) {
                    $flag = 1;
                }
                break;
            // 基本的にbattle
            case $channels[8]['hash']:
                $flag = 1;
                if ($this->array_strpos($title, $keywords['song']) === true) {
                    $flag = 0;
                }
                if ($this->array_strpos($title, $keywords['8']) === true) {
                    $flag = 3;
                }
                break;
            // 基本的にbattle
            case $channels[9]['hash']:
                $flag = 1;
                if ($this->array_strpos($title, $keywords['song']) === true) {
                    $flag = 0;
                }
                break;
            // 基本的にsong
            case $channels[23]['hash']:
                if ($this->array_strpos($title, $keywords['23']) === true) {
                    $flag = 1;
                }
                break;
            case $channels[31]['hash']:
                $flag = 2;
                break;
            case $channels[33]['hash']:
                $flag = 2;
                break;
            default:
                break;
        }

        // インタビューは全てのチャンネルにまたがるため、全てのタイトルをチェックする
        if ($this->array_strpos($title, $keywords['interview']) === true) {
            $flag = 2;
        }

        switch ($flag) {
            case 0:
                $genre = 'MV';
                break;
            case 1:
                $genre = 'battle';
                break;
            case 2:
                $genre = 'interview';
                break;
             case 3:
                 $genre = 'others';
                 break;
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
    private function array_strpos(string $haystack, array $needles, int $offset = 0): bool
    {
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle, $offset) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * チャンネルの動画数を取得する
     *
     * @param string $hash
     * @return int
     * @throws \Exception
     */
    public function getVideoCountByChannelHash(string $hash): int
    {
        $res = $this->youtube::getChannelById($hash);
        return $res->statistics->videoCount;
    }
}
