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
        $after = $this->video_repo->fetchLatestPublishedVideoRecord()->published_at;
        $before = substr($now->format(\DateTime::ATOM), 0, 19) . '.000Z';
        $channel_data = $res = [];

        foreach ($this->channel_repo->fetchAll() as $query) {
            $res = $this->youtube->listChannelVideos($query->hash, 50, $after, $before);
            if (!$res) {
                continue;
            }
            $channel_data[] = $res;
        }

//        $json = json_encode($channel_data, JSON_UNESCAPED_UNICODE);
//        $file = base_path( "tests/json/channel_some_data.json");
//        file_put_contents($file, $json);

        return $channel_data;
    }

}
