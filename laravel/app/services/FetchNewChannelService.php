<?php

namespace App\Services;

use App\Repositories\ChannelRepository;
use App\Repositories\ChannelThumbnailRepository;
use App\Repositories\ApiRepository;
use App\Repositories\DownloadJpgFileRepository;

class FetchNewChannelService
{
    private $channel_repo;
    private $channel_thumbnail_repo;
    private $api_repo;
    private $jpg_repo;

    const sizes = ['std', 'medium', 'high'];

    public function __construct
    (
        ChannelRepository $channel_repo,
        ChannelThumbnailRepository $channel_thumbnail_repo,
        ApiRepository $api_repo,
        DownloadJpgFileRepository $jpg_repo
    )
    {
        $this->channel_repo = $channel_repo;
        $this->channel_thumbnail_repo = $channel_thumbnail_repo;
        $this->api_repo = $api_repo;
        $this->jpg_repo = $jpg_repo;
    }

    /**
     * commandから呼び出す
     *
     * @param array $channels
     * @return int
     * @throws \Exception
     */
    public function run(array $channels): int
    {
        $new_channels = $this->getNewChannels($channels);
        if (empty($new_channels)) return 0;
        $channel_thumbnails = $this->saveChannelsAndChannelThumbnails($new_channels);
        $this->downloadImages($channel_thumbnails);
        return count($channel_thumbnails);
    }

    /**
     * JSONファイルに追記されたchannelを取得する
     *
     * @param array $channels
     * @return array
     */
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

    /**
     * 新しいchannelとchannel_thumbnailをDBに保存する
     *
     * @param array $new_channels
     * @return array
     * @throws \Exception
     */
    private function saveChannelsAndChannelThumbnails(array $new_channels): array
    {
        $channel_thumbnails = [];
        foreach ($new_channels as $channel) {
            [$channel_array, $channel_thumbnail_array] = $this->api_repo->getChannelByHash($channel['hash']);
            $saved_channel = $this->channel_repo->saveRecord($channel_array);
            $channel_thumbnail_array['channel_id'] = $saved_channel['id'];
            $channel_thumbnails[] = $this->channel_thumbnail_repo->saveRecord($channel_thumbnail_array);
        }
        return $channel_thumbnails;
    }

    /**
     * channelのサムネイル画像をダウンロードする
     *
     * @param array $channel_thumbnails
     */
    private function downloadImages(array $channel_thumbnails): void
    {
        foreach ($channel_thumbnails as $record) {
            foreach (self::sizes as $size) {
                $this->downloadThumbnails($record, $size);
            }
        }
    }

    /**
     * file_get_contentsで画像を取得する
     *
     * @param object $record
     * @param string $size
     */
    private function downloadThumbnails($record, string $size): void
    {
        $table = $this->channel_thumbnail_repo->getTableName();
        $url = str_replace('_live', '', $record->{$size});
        if (!$hash = $this->channel_repo->getHashFromChannelThumbnail($record)) return;
        $file_path = "image/{$table}/{$size}/{$hash}.jpg";
        if (file_exists(public_path($file_path))) return;

        $result = $this->jpg_repo->couldDownloadJpgFromUrl($url, $file_path);
        if ($result === false) {
            Log::warning('Cannot download image file from: ' . $url);
            $this->channel_repo->deleteByHash($hash);
            $this->channel_thumbnail_repo->deleteById($record->id);
            // $hashを使って画像も消す
        }
    }
}
