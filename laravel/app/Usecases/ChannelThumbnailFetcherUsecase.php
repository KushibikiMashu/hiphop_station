<?php

namespace App\Usecases;

use App\Services\ChannelThumbnailFetcherService;

class ChannelThumbnailFetcherUsecase
{
    private $service;

    public function __construct(ChannelThumbnailFetcherService $service)
    {
        $this->service = $service;
    }

    public function run() :void
    {
        $this->service->downloadImages();
    }
}
