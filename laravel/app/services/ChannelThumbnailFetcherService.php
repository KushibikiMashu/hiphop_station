<?php

namespace App\Services;

use App\Repositories\ChannelRepository;
use App\Repositories\ChannelThumbnailRepository;
use App\Repositories\DownloadJpgFileRepository;
use Illuminate\Support\Facades\Log;

class ChannelThumbnailFetcherService
{
    private $sizes = ['std', 'medium', 'high'];
    private $channel_repository;
    private $channel_thumbnail_repository;
    private $download_jpg_file_repository;

    public function __construct(
        ChannelRepository $channel_repository,
        ChannelThumbnailRepository $channel_thumbnail_repository,
        DownloadJpgFileRepository $download_jpg_file_repository
    )
    {
        $this->channel_repository = $channel_repository;
        $this->channel_thumbnail_repository = $channel_thumbnail_repository;
        $this->download_jpg_file_repository = $download_jpg_file_repository;
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
        foreach ($this->channel_thumbnail_repository->fetchAll() as $record) {
            foreach ($this->sizes as $size) {
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
        $table = $this->channel_thumbnail_repository->getTableName();
        $url = $record->{$size};
        if (!$hash = $this->channel_repository->getHashFromChannelThumbnail($record)) return;
        $file_path = "image/{$table}/{$size}/{$hash}.jpg";
        if (file_exists(public_path($file_path))) return;

        $result = $this->download_jpg_file_repository->couldDownloadJpgFromUrl($url, $file_path);
        if ($result === false) {
            Log::warning('Cannot download image file from: ' . $url);
            $this->channel_repository->deleteByHash($hash);
            $this->channel_thumbnail_repository->deleteById($record->id);
            // $hashを使って画像も消す
        }
    }
}
