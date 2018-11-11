<?php

namespace App\Usecases;

use App\Services\VideoThumbnailFetcherService;

class VideoThumbnailFetcherUsecase
{
    private $service;

    public function __construct(VideoThumbnailFetcherService $service)
    {
        $this->service = $service;
    }

    public function run() :void
    {
        $this->service->downloadImages();
    }
}
