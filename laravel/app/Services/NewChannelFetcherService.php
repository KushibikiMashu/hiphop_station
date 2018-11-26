<?php

namespace App\Services;

use App\Repositories\ChannelRepository;
use App\Repositories\ChannelThumbnailRepository;
use App\Repositories\ApiRepository;
use App\Repositories\DownloadJpgFileRepository;

class NewChannelFetcherService extends BaseService
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * commandから呼び出す
     *
     * @param array $channels
     * @return bool
     * @throws \Exception
     */
    public function run(array $channels): bool
    {
        $new_channels = $this->getNewChannels($channels);
        if (empty($new_channels)) return false;
        $this->saveChannelsAndThumbnails($new_channels);
        $this->downloadChannelThumbnails();
        return true;
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
     * @throws \Exception
     */
    private function saveChannelsAndThumbnails(array $new_channels): void
    {
        foreach ($new_channels as $channel) {
            [$channels, $channel_thumbnails] = $this->api_repo->getChannelByHash($channel['hash']);
            $saved_channel = $this->channel_repo->saveRecord($channels);
            $channel_thumbnails['channel_id'] = $saved_channel['id'];
            $this->channel_thumbnail_repo->saveRecord($channel_thumbnails);
        }
    }

    /**
     * channelのサムネイル画像をダウンロードする
     */
    private function downloadChannelThumbnails(): void
    {
        $five_minutes_ago = \Carbon\Carbon::now()->subMinutes(5);
        $channel_thumbnails = $this->channel_thumbnail_repo->fetchRecordsOfOverTheLastFiveMinutes($five_minutes_ago);
        foreach ($channel_thumbnails as $record) {
            foreach (config('const.SIZES') as $size) {
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
        if (!$hash = $this->channel_repo->getHashByChannelThumbnail($record)) return;
        $file_path = "image/{$table}/{$size}/{$hash}.jpg";
        if (file_exists(public_path($file_path))) return;

        $result = $this->jpg_repo->couldDownloadJpgFromUrl($url, $file_path);
        if ($result === false) {
            Log::warning('Cannot download image file from: ' . $url);
            $this->channel_repo->deleteByHash($hash);
            $this->channel_thumbnail_repo->deleteById($record->id);
        }
    }
}
