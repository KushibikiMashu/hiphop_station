<?php

namespace App\Services;

use App\Repositories\ChannelRepository;
use App\Repositories\ChannelThumbnailRepository;
use App\Repositories\DownloadJpgFileRepository;

class ChannelThumbnailFetcherService extends BaseService
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 外部からこのクラスを利用するための関数
     */
    public function run(): void
    {
        $this->downloadImages();
    }

    /**
     * channel_thumbnailテーブルに格納されているアドレスの画像をダウンロードする
     */
    private function downloadImages(): void
    {
        $generator = $this->channel_thumbnail_repo->fetchAll();
        foreach ($generator as $record) {
            foreach (config('const.SIZES') as $size) {
                $this->fetchThumbnailInDatabase($record, $size);
            }
        }
    }

    /**
     * file_get_contentsで画像を取得する
     *
     * @param object $record
     * @param string $size
     */
    private function fetchThumbnailInDatabase($record, string $size): void
    {
        $table = $this->channel_thumbnail_repo->getTableName();
        $url = $record->{$size};
        if (!$hash = $this->channel_repo->getHashByChannelThumbnail($record)) return;
        $file_path = "image/{$table}/{$size}/{$hash}.jpg";
        if (file_exists(public_path($file_path))) return;
        dump($file_path); // あえて残す

        $result = $this->jpg_repo->couldDownloadJpgFromUrl($url, $file_path);
        if ($result === false) {
            \Log::warning('Cannot download image file from: ' . $url);
            $this->channel_repo->deleteByHash($hash);
            $this->channel_thumbnail_repo->deleteById($record->id);
        }
    }
}
