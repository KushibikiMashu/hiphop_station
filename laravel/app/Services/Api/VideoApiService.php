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
    public function getAllVideos(): array
    {
        $new_videos  = [];
        $videos      = $this->video_repo->getAllVideoJoinedChannel();
        foreach ($videos as $video) {
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
        $video->shortTitle = mb_strimwidth($video->title, 0, 50, '...');
        $video->diffDate   = getDateDiff($video->publishedAt);
        $video->thumbnail  = '/image/video_thumbnail/' . config('const.SIZES')[2] . "/{$video->hash}.jpg";
        return $video;
    }
}
