<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use App\Repositories\VideoThumbnailRepository;
use App\Repositories\DownloadJpgFileRepository;
use Illuminate\Support\Facades\Log;

class VideoThumbnailFetcherService
{
    private $video_repo;
    private $video_thumbnail_repo;
    private $download_jpg_file_repo;

    public function __construct(
        VideoRepository $video_repo,
        VideoThumbnailRepository $video_thumbnail_repo,
        DownloadJpgFileRepository $download_jpg_file_repo
    )
    {
        $this->video_repo = $video_repo;
        $this->video_thumbnail_repo = $video_thumbnail_repo;
        $this->download_jpg_file_repo = $download_jpg_file_repo;
    }

    /**
     * 外部からこのクラスを利用するための関数
     */
    public function run(): void
    {
        $this->downloadImages();
    }

    /**
     * video_thumbnailテーブルに格納されているアドレスの画像をダウンロードする
     */
    private function downloadImages(): void
    {
        $generator = $this->video_thumbnail_repo->fetchAll();
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
        $table = $this->video_thumbnail_repo->getTableName();
        $url = str_replace('_live', '', $record->{$size});
        if (!$hash = $this->video_repo->getHashFromVideoThumbnail($record)) return;
        $file_path = "image/{$table}/{$size}/{$hash}.jpg";
        if (file_exists(public_path($file_path))) return;
        dump($file_path); // あえて残す

        $result = $this->download_jpg_file_repo->couldDownloadJpgFromUrl($url, $file_path);
        if ($result === false) {
            Log::warning('Cannot download image file from: ' . $url);
            $this->video_repo->deleteByHash($hash);
            $this->video_thumbnail_repo->deleteById($record->id);
        }
    }
}
