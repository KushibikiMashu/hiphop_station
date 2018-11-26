<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use App\Repositories\VideoThumbnailRepository;
use App\Repositories\ChannelRepository;
use App\Repositories\ApiRepository;
use App\Repositories\DownloadJpgFileRepository;

abstract class BaseService
{
    protected $video_repo;
    protected $video_thumbnail_repo;
    protected $channel_repo;
    protected $api_repo;
    protected $jpg_repo;

    public function __construct()
    {
        $this->video_repo           = new VideoRepository;
        $this->video_thumbnail_repo = new VideoThumbnailRepository;
        $this->channel_repo         = new ChannelRepository;
        $this->api_repo             = new ApiRepository;
        $this->jpg_repo             = new DownloadJpgFileRepository;
    }
}





