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

    public function run(array $channels): void
    {
        $new_channels = $this->getNewChannels($channels);
        $this->saveChannelsAndChannelThumbnails($new_channels);
        // download channel_thumbnail
    }

    private function getNewChannels(array $channels): array
    {
        $new_channel = [];
        foreach ($channels as $key => $channel) {
            if ($this->channel_repo->channel_exists('hash', $channel['hash'])) {
                continue;
            }
            $new_channel[] = $channel;
        }
        return $new_channel;
    }

    private function saveChannelsAndChannelThumbnails(array $new_channels):void
    {
        foreach ($new_channels as $channel) {
            $this->channel_repo->saveRecord($this->api_repo->getChannelByHash($channel['hash']));
        }
    }

}
