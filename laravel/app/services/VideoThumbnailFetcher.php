<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use App\Repositories\VideoThumbnailRepository;
use Illuminate\Support\Facades\Log;

class VideoThumbnailFetcher
{
    private $video_repository;
    private $video_thumbnail_repository;
    private $sizes = ['std', 'medium', 'high'];


    public function __construct(VideoRepository $video_repository, VideoThumbnailRepository $video_thumbnail_repository)
    {
        $this->video_repository = $video_repository;
        $this->video_thumbnail_repository = $video_thumbnail_repository;
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

        $result = $this->canDownloadJpgFileFromUrl($url, $image_path);
        if (!$result) {
            Log::warning('Cannot download image file from: ' . $url);
            $this->deleteInvalidRecords($record->id, $hash);
        }
    }

    // 別のRepository作って、そこに実装してもいいと思う。OnlineImageRepositoryとか。外部からのデータ取得なので、オニオン型の一番外だから
    private function canDownloadJpgFileFromUrl(string $url, string $image_path): bool
    {
        // レスポンスコードが400系・500系でもwarningを出さない
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $data = file_get_contents($url, false, $context);
        if (strpos($http_response_header[0], '200') !== false) {
            file_put_contents(public_path($image_path), $data);
            return true;
        }
        return false;
    }

    private function deleteInvalidRecords(int $id, string $hash): void
    {
        $this->video_repository->deleteByHash($hash);
        $this->video_thumbnail_repository->deleteById($id);
        // $hashを使って画像も消す
    }



}
