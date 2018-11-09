<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use App\Repositories\VideoThumbnailRepository;

class VideoThumbnailFetcher
{
    private $video_repository;
    private $video_thumbnail_repository;

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


    private function deleteInvalidRecords(int $id, string $hash): void
    {
        $this->video_repository->deleteByHash($hash);
        $this->video_thumbnail_repository->deleteById($id);
        // $hashを使って画像も消す
    }



}
