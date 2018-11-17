<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\VideoRepository;
use App\Console\Commands\FetchLatestVideosFromYoutubeApi;


class ForSimpleTest extends TestCase
{
    private $instance;
    private $table = 'video';

    public function setUp(): void
    {
        parent::setup();
        $this->instance = new VideoRepository;
    }

    /**
     * @test
     *
     */
    public function fetchLatestPublishedVideoRecord__最新のpublished_atのレコードを取得する(): void
    {
        $actual = $this->instance->fetchLatestPublishedVideoRecord()['published_at'];
        $expected = (new FetchLatestVideosFromYoutubeApi)->fetch_max_published_datetime();
        $this->assertSame($expected, $actual);
    }
}
