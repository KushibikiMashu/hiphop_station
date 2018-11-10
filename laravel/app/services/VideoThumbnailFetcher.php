<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use App\Repositories\VideoThumbnailRepository;
use App\Repositories\DownloadJpgFileRepository;
use Illuminate\Support\Facades\Log;

class VideoThumbnailFetcher
{
    private $video_repository;
    private $video_thumbnail_repository;
    private $download_jpg_file_repository;
    private $sizes = ['std', 'medium', 'high'];

    public function __construct(VideoRepository $video_repository, VideoThumbnailRepository $video_thumbnail_repository, DownloadJpgFileRepository $download_jpg_file_repository)
    {
        $this->video_repository = $video_repository;
        $this->video_thumbnail_repository = $video_thumbnail_repository;
        $this->download_jpg_file_repository = $download_jpg_file_repository;
    }

    public function run(int $id, string $hash)
    {
        // runで全て終えるようにする
        $this->deleteInvalidRecords($id, $hash);
    }

    /**
     * XXX_thumbnailテーブルに格納されているアドレスの画像をダウンロードする
     */
    public function downloadImages(): void
    {
        foreach ($this->video_thumbnail_repository->fetchAll() as $record) {
            foreach ($this->sizes as $size) {
                $this->fetchThumbnailInDatabase($record, $size);
            }
        }
    }

    /**
     * file_get_contentsで画像を取得する
     *
     */
    private function fetchThumbnailInDatabase($record, string $size): void
    {
        $table = $this->video_thumbnail_repository->getTableName();
        $url = str_replace('_live', '', $record->{$size});
        if (!$hash = $this->video_repository->getHashFromVideoThumbnail($record)) return;
        $image_path = "image/{$table}/{$size}/{$hash}.jpg";
        if (file_exists(public_path($image_path))) return;

        $result = $this->download_jpg_file_repository->canDownloadJpgFromUrl($url, $image_path);
        if (!$result) {
            Log::warning('Cannot download image file from: ' . $url);
            $this->video_repository->deleteByHash($hash);
            $this->video_thumbnail_repository->deleteById($record->id);
            // $hashを使って画像も消す
        }
    }
}
