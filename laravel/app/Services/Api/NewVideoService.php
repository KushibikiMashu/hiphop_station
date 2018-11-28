<?php

namespace App\Services\Api;

class NewVideoService extends \App\Services\BaseService
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * published_atが１週間以内のデータを取得し、APIで返却する形に整形する
     *
     * @return array
     */
    public function getNewVideo(): array
    {
        $new_videos = [];
        $videos     = $this->video_repo->getVideosOfThisWeek();
        foreach ($videos as $video) {
            $video['title']        = mb_strimwidth($video['title'], 0, 50, '...');
            $video['published_at'] = (new \Carbon\Carbon($video['published_at']))->format('Y-m-d H:i:s');
            $video['diff_date']    = getDateDiff($video['published_at']);
            $video['thumbnail']    = [
                'high' => '/image/video_thumbnail/' . config('const.SIZES')[2] . "/{$video['hash']}.jpg"
            ];
            // channelへの参照がボトルネック。全部取得して配列でなんとかする。キーはchannel_id
            $channel                   = $this->channel_repo->fetchChannelByChannelId($video['channel_id']);
            $video['channel']['title'] = $channel->title;
            unset($video['channel_id'], $video['created_at'], $video['updated_at']);
            $new_videos[] = $video;
        }
        return $new_videos;
    }

}
