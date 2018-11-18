<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\VideoRepository;
use App\Console\Commands\FetchLatestVideosFromYoutubeApi;
use Illuminate\Support\Facades\DB;
use App\Video;

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
     */
    public function simpleTest()
    {
        $query = Video::select('id')->get()->toArray();
        $expected = [];
        foreach ($query as $ids) {
            $expected[] = $ids['id'];
        }
        $actual = $this->instance->fetchPluckedColumn('id')->toArray();
        $this->assertSame($expected, $actual);
        $this->assertTrue(True);
    }

}
