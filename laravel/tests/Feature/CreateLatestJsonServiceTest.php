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
        $v = new VideoRepository;
        $c = new ChannelRepository;

        [$videos, $channels] = $this->instance->getVideoAndChannelRecordArray();
        $video_actual = $v->fetchColumnsOrderByPublishedAt('id', 'channel_id', 'title', 'hash', 'genre', 'published_at');
        $channel_actual = $c->fetchColumnsOrderById('id', 'title', 'hash');
        $this->assertSame($video_actual, $videos);
        $this->assertSame($channel_actual, $channels);

    }
}

