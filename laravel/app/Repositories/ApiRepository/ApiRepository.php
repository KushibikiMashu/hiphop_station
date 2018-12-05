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
        $after  = substr((new Carbon($this->video_repo->fetchLatestPublishedAtVideoRecord()->published_at))
                ->format(\DateTime::ATOM), 0, 19) . '.000Z';
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
            'published_at' => (new Carbon($res->snippet->publishedAt))->format('Y-m-d H:i:s'),
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

        return $this->processResponse($channel_id, $channel_hash, $res);
    }

    public function getNewVideosByChannelHashUnderFiftyVideos(int $channel_id, string $channel_hash, int $maxResult): array
    {
        $res = $this->extended_youtube->listChannelVideos($channel_hash, $maxResult);
        if ($res === false) return [null, null];
        return $this->processResponse($channel_id, $channel_hash, $res);
    }

    private function processResponse($channel_id, $channel_hash, $res): array
    {
        $videos                  = $video_thumbnails = [];
        $registered_video_hashes = $this->video_repo->fetchPluckedColumn('hash')->flip();
        foreach ($res as $data) {
            if (isset($registered_video_hashes[$data->id->videoId])) continue;
            $title    = $data->snippet->title;
            $hash     = $data->id->videoId;
            $genre    = $this->getGenre($channel_id, $channel_hash, $title);
            $videos[] = [
                'channel_id'   => $channel_id,
                'title'        => $title,
                'hash'         => $hash,
                'genre'        => $genre,
                'published_at' => (new \Carbon\Carbon($data->snippet->publishedAt))->format('Y-m-d H:i:s')
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
     * @param string $channel_hash
     * @param string $title
     * @return string
     */
    public function getGenre(int $channel_id, string $channel_hash, string $title): string
    {
        /**
         * titleとchannel_idでgenreを分類する
         * $flagで状態を持つ
         */
        $channels = config('channels');
        $keywords = config('const.KEYWORDS');
        $flag     = 0;

//        $genres = ['MV', 'radio']; // MV, radioなどジャンル名を配列で持たせる→constに定義
//        $g = 'MV';
//        foreach ($genres as $genre) {
//            if (!isset($keywords[$channel_id][$genre])) continue;
//            if (arrayStrpos($title, $keywords[$channel_id][$genre])) {
//                $g = $genre;
//            }
//        }
//        dump($title);
//        dump($g);

        switch ($channel_hash) {
            case $channels[1]['hash']:
                if (arrayStrpos($title, $keywords[1]['radio'])) {
                    $flag = 3;
                }
                break;
            case $channels[2]['hash']:
                if (arrayStrpos($title, $keywords[2]['battle'])) {
                    $flag = 1;
                } elseif (arrayStrpos($title, $keywords[2]['others'])) {
                    $flag = 98;
                }
                break;
            case $channels[7]['hash']:
                if (arrayStrpos($title, $keywords[7]['others'])) {
                    $flag = 98;
                }
                break;
            case $channels[8]['hash']: // 基本的にbattle
                $flag = 1;
                if (arrayStrpos($title, $keywords[8]['MV'])) {
                    $flag = 0;
                } elseif (arrayStrpos($title, $keywords[8]['interview'])) {
                    $flag = 2;
                } elseif (arrayStrpos($title, $keywords[8]['radio'])) {
                    $flag = 3;
                }
                break;
            case $channels[9]['hash']: // 基本的にbattle
                $flag = 1;
                if (arrayStrpos($title, $keywords[9]['MV'])) {
                    $flag = 0;
                } elseif (arrayStrpos($title, $keywords[9]['interview'])) {
                    $flag = 2;
                }
                break;
            case $channels[10]['hash']:
                if (arrayStrpos($title, $keywords[10]['interview'])) {
                    $flag = 2;
                }
                break;
            case $channels[20]['hash']:
                if (arrayStrpos($title, $keywords[20]['others'])) {
                    $flag = 98;
                }
                break;
            case $channels[21]['hash']:
                $flag = 99;
                if (arrayStrpos($title, $keywords[21]['MV'])) {
                    $flag = 0;
                }
                break;
            case $channels[23]['hash']:
                if (arrayStrpos($title, $keywords[23]['battle'])) {
                    $flag = 1;
                }
                break;
            case $channels[24]['hash']:
                $flag = 99;
                break;
            case $channels[29]['hash']:
                $flag = 98;
                if (arrayStrpos($title, $keywords[29]['MV'])) {
                    $flag = 0;
                }
                break;
            case $channels[31]['hash']:
                $flag = 98;
                break;
            case $channels[33]['hash']:
                $flag = 2;
                break;
            case $channels[37]['hash']:
                $flag = 98;
                if (arrayStrpos($title, $keywords[37]['MV'])) {
                    $flag = 0;
                }
                break;
            case $channels[38]['hash']:
                if (arrayStrpos($title, $keywords[38]['others'])) {
                    $flag = 98;
                }
                break;
            case $channels[39]['hash']:
                $flag = 99;
                if (arrayStrpos($title, $keywords[38]['others'])) {
                    $flag = 98;
                }
                break;
            case $channels[41]['hash']:
                if (arrayStrpos($title, $keywords[41]['others'])) {
                    $flag = 98;
                }
                break;
            default:
                break;
        }
        return $this->determineVideoGenre($flag);
    }

    /**
     * 動画のジャンルを決定する
     *
     * @param int $flag
     * @return string
     */
    private function determineVideoGenre(int $flag): string
    {
        $genre = 'not HIPHOP';
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
                $genre = 'radio';
                break;
            case 98:
                $genre = 'others';
                break;
            case 99:
                $genre = 'not HIPHOP';
                break;
            default:
                break;
        }
        return $genre;
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
