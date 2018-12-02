<?php

namespace App\Services;

class CreateLatestJsonService extends BaseService
{
    private $service;

    public function __construct(Api\VideoApiService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function getArrays(): array
    {
        $videos   = $this->service->getAllVideos();
        $channels = $this->channel_repo->fetchAnyColumns('title', 'hash');
        return [$channels, $videos];
    }
}
