<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\CreateLatestJsonService;
use App\Repositories\VideoRepository;
use App\Repositories\ChannelRepository;

class CreateLatestJsonServiceTest extends TestCase
{
    private $instance;

    public function setUp(): void
    {
        parent::setup();
        $this->instance = new CreateLatestJsonService(new VideoRepository, new ChannelRepository);
    }

    /**
     * @test
     */
    public function getVideoAndChannelRecordArray__videoとchannel()
    {
        $i = new VideoRepository;

        [$videos, $channels] = $this->instance->getVideoAndChannelRecordArray();
        $actual = $i->fetchColumnsOrderByPublishedAt('channel_id', 'title', 'hash', 'genre')->toArray();
        $this->assertSame($actual, $videos);

    }
}
