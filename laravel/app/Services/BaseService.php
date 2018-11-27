<?php

namespace App\Services;

abstract class BaseService
{
    protected $video_repo;
    protected $video_thumbnail_repo;
    protected $channel_thumbnail_repo;
    protected $channel_repo;
    protected $api_repo;
    protected $jpg_repo;

    public function __construct()
    {
        $this->video_repo             = new \App\Repositories\VideoRepository;
        $this->video_thumbnail_repo   = new \App\Repositories\VideoThumbnailRepository;
        $this->channel_thumbnail_repo = new \App\Repositories\ChannelThumbnailRepository;
        $this->channel_repo           = new \App\Repositories\ChannelRepository;
        $this->api_repo               = new \App\Repositories\ApiRepository;
        $this->jpg_repo               = new \App\Repositories\DownloadJpgFileRepository;
    }
}





