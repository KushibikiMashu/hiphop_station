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
        $channels = $this->channel_repo->fetchAnyColumns('title', 'hash');
        $main = $this->addExtraData($videos);
        return [$channels, $main];
    }

    /**
     * 動画に紐づくchannel情報とサムネイルのURLを追加する
     *
     * @param $videos
     * @return array
     */
    private function addExtraData($videos): array
    {
        $new_query = [];
        $sizes = ['std', 'medium', 'high'];
        foreach ($videos as $record) {
            $channel = \App\Channel::where('id', $record['channel_id'])->get()[0]; // channel_repoに書く
            $record['channel']['title'] = $channel->title;
            $record['channel']['hash'] = $channel->hash;
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
