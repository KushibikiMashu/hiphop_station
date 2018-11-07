<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\ChannelThumbnail;
use App\Console\Commands\FetchVideoThumbnailImage;

class ThumbnailImageFetcherDependsOnFetchChannelThumbnailImageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }
}
