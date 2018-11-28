<?php

namespace App\Services\Api;

class VideoApiService extends \App\Services\BaseService
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
    public function getVideosOfThisTwoWeeks(): array
    {
        $new_videos  = [];
        $videos      = $this->video_repo->getAllVideoJoinedChannelTwoWeeks();
        $twoWeeksAgo = (new \Carbon\Carbon)->subWeeks(2)->format('Y-m-d H:i:s');
        foreach ($videos as $video) {
            if ((new \Carbon\Carbon($video->published_at)) < $twoWeeksAgo) continue;
            $new_videos[] = $this->addOtherData($video);
        }
        return $new_videos;
    }

    /**
     * videoの周辺情報を追加する
     *
     * @param $video
     * @return mixed
     */
    private function addOtherData($video)
    {
        $video->title     = mb_strimwidth($video->title, 0, 50, '...');
        $video->diff_date = getDateDiff($video->published_at);
        $video->thumbnail = '/image/video_thumbnail/' . config('const.SIZES')[2] . "/{$video->hash}.jpg";
        return $video;
    }
}
