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
            'std'        => $res->snippet->thumbnails->default->url,
            'medium'     => $res->snippet->thumbnails->medium->url,
            'high'       => $res->snippet->thumbnails->high->url,
        ];
        return [$channel, $channel_thumbnail];
    }
}
