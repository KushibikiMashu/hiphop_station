<?php

namespace App\Services;

class CreateLatestJsonService extends BaseService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getArrays(): array
    {
        $videos   = $this->video_repo->fetchColumnsOrderByPublishedAt('id', 'channel_id', 'title', 'hash', 'genre', 'published_at');
        $channels = $this->channel_repo->fetchAnyColumns('title', 'hash');
        $main     = $this->addExtraData($videos);
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
        foreach ($videos as $video) {
            $video['title']            = mb_strimwidth($video['title'], 0, 50, '...');
            $video['diff_date']        = getDateDiff($video['published_at']);
            $video['thumbnail']        = [
                'std'    => '/image/video_thumbnail/' . config('const.SIZES')[0] . "/{$video['hash']}.jpg",
                'medium' => '/image/video_thumbnail/' . config('const.SIZES')[1] . "/{$video['hash']}.jpg",
                'high'   => '/image/video_thumbnail/' . config('const.SIZES')[2] . "/{$video['hash']}.jpg"
            ];
            $channel                   = $this->channel_repo->fetchChannelByChannelId($video['channel_id']);
            $video['channel']['title'] = $channel->title;
            $video['channel']['hash']  = $channel->hash;
            $new_query[]               = $video;
        }
        return $new_query;
    }
}
