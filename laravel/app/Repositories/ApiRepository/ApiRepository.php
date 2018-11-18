<?php

namespace App\Repositories;

use Illuminate\Support\Carbon;
use App\Services\CustomizedYoutubeApi;

class ApiRepository implements ApiRepositoryInterface
{
    private $video_repo;
    private $channel_repo;
    private $youtube;

    public function __construct(
        VideoRepository $video_repo,
        ChannelRepository $channel_repo,
        CustomizedYoutubeApi $youtube
    )
    {
        $this->video_repo = $video_repo;
        $this->channel_repo = $channel_repo;
        $this->youtube = $youtube;
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
                $res = $this->youtube->listChannelVideos($hash, 50, $after, $before);
                // 新しいvideoがない場合(false)か、基準になる日付の動画は配列$videosに追加しない
                if ($res === false || (count($res) === 1 && $res[0]->snippet->publishedAt === $after)) {
                    continue;
                }
                $videos[] = $res;
            }
        }
        return $videos;
    }

}
