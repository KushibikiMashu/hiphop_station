<?php

namespace App\Services;

use App\Repositories\ChannelRepository;
use App\Repositories\VideoRepository;
use App\Repositories\VideoThumbnailRepository;
use App\Repositories\ApiRepository;
use App\Video;

class NewVideoFetcherService
{
    private $channel_repo;
    private $video_repo;
    private $video_thumbnail_repo;
    private $api_repo;
    private $youtube;

    const sizes = ['std', 'medium', 'high'];

    public function __construct
    (
        ChannelRepository $channel_repo,
        VideoRepository $video_repo,
        VideoThumbnailRepository $video_thumbnail_repo,
        ApiRepository $api_repo,
        CustomizedYoutubeApi $youtube
    )
    {
        $this->channel_repo = $channel_repo;
        $this->video_repo = $video_repo;
        $this->video_thumbnail_repo = $video_thumbnail_repo;
        $this->api_repo = $api_repo;
        $this->youtube = $youtube;
    }

    /**
     * commandから呼び出す
     *
     */
    public function run()
    {
        $hashes = $this->getNewChannelHash();
        dd($hashes);

        // loop文でlistChannelVideos()を適用する
        // １週間ずつ取得する

        // Videoに登録する
        // Video_Thumbnailに登録する
        // (fetch:allVideoThumbnailコマンドを実行する)

    }

    /**
     * 紐づくvideoがないchannelのhashを取得する
     *
     * @return array
     */
    private function getNewChannelHash(): array
    {
        $hashes = [];
        foreach ($this->channel_repo->fetchAll() as $record) {
            if($this->video_repo->channelVideoExists($record->id)) {
                $hashes[] = $record->hash;
            }
        }
        return $hashes;
    }

//    private function


}
