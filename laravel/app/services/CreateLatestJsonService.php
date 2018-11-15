<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use App\Repositories\ChannelRepository;

class CreateLatestJsonService
{
    private $video_repository;
    private $channel_repository;

    public function __construct(VideoRepository $video_repository, ChannelRepository $channel_repository)
    {
        $this->video_repository = $video_repository;
        $this->channel_repository = $channel_repository;
    }

    public function getArrays() :array
    {
        $videos = $this->video_repository->fetchColumnsOrderByPublishedAt('id', 'channel_id', 'title', 'hash', 'genre', 'published_at');
        $channels = $this->channel_repository->fetchColumnsOrderById('id', 'title', 'hash');
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
            dd($record);
            $record['channel'] = $channels[$record['channel_id'] - 1]; // これだけ別の関数に出す
            $record['thumbnail'] = 'https://i.ytimg.com/vi/' . $record['hash'] . '/hqdefault.jpg';
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
