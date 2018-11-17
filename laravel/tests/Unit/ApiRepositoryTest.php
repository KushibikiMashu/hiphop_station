<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\ApiRepository;
use App\Repositories\VideoRepository;
use App\Repositories\ChannelRepository;

class ApiRepositoryTest extends TestCase
{
    private $instance;

    public function setUp()
    {
        parent::setUp();
        $this->instance = new ApiRepository(new VideoRepository, new ChannelRepository);
    }

    public function newVideosOfRegisteredChannel__最新の動画を取得する()
    {
        // 作れるのか？
    }
}
