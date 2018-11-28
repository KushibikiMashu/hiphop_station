<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class ApiControllerTest extends TestCase
{
    private $instance;

    public function setUp()
    {
        parent::setUp();
        $this->instance = new \App\Http\Controllers\ApiController;
    }

    /**
     * @test
     */
    public function getNew__新着動画をJSONで返す()
    {
        $video    = self::createVideoRecord();
        $response = $this->get('/api/video/new');
        $response->assertStatus(200);
        self::deleteRecordByTableAndId($video->getTable(), $video->id);
    }
}
