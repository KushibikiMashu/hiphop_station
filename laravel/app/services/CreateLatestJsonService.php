<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use App\Repositories\ChannelRepository;

class CreateLatestJsonService
{
    private $video_repo;
    private $channel_repo;

    public function __construct(VideoRepository $video_repo, ChannelRepository $channel_repo)
    {
        $this->video_repo = $video_repo;
        $this->channel_repo = $channel_repo;
    }

    public function getArrays() :array
    {
        $videos = $this->video_repo->fetchColumnsOrderByPublishedAt('id', 'channel_id', 'title', 'hash', 'genre', 'published_at');
        $channels = $this->channel_repo->fetchColumnsOrderById('title', 'hash');
        $main = $this->addExtraData($videos, $channels);
        return [$channels, $main];
    }

    /**
     * 動画に紐づくchannel情報とサムネイルのURLを追加する
     *
     * @param $videos
     * @param array $channels
     * @return array
     */
    private function addExtraData($videos, array $channels): array
    {
        $new_query = [];
        $sizes = ['std', 'medium', 'high'];
        foreach ($videos as $record) {
            $record['channel'] = $channels[$record['channel_id'] - 1];
            $record['thumbnail'] = [
                'std'    => "/image/video_thumbnail/$sizes[0]/{$record['hash']}.jpg",
                'medium' => "/image/video_thumbnail/$sizes[1]/{$record['hash']}.jpg",
                'high'   => "/image/video_thumbnail/$sizes[2]/{$record['hash']}.jpg"
            ];
            $new_query[] = $record;
        }
        return $new_query;
    }
}
