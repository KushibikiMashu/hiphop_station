<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use App\Repositories\VideoThumbnailRepository;
use App\Repositories\ChannelRepository;
use App\Repositories\ApiRepository;
use App\Repositories\DownloadJpgFileRepository;

class FetchNewChannelService
{
    private $video_repo;
    private $video_thumbnail_repo;
    private $channel_repo;
    private $api_repo;
    private $jpg_repo;

    public function __construct
    (
        VideoRepository $video_repo,
        VideoThumbnailRepository $video_thumbnail_repo,
        ChannelRepository $channel_repo,
        ApiRepository $api_repo,
        DownloadJpgFileRepository $jpg_repo
    )
    {
        $this->video_repo = $video_repo;
        $this->video_thumbnail_repo = $video_thumbnail_repo;
        $this->channel_repo = $channel_repo;
        $this->api_repo = $api_repo;
        $this->jpg_repo = $jpg_repo;
    }

    public function run(array $channel_array):void
    {
        foreach ($channel_array as $key => $channel) {
            if($this->channel_repo->channel_exists('hash', $channel['hash'])) {
                unset($channel_array[$key]);
            }
        }
    }
}
