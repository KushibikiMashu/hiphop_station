<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Video;
use App\VideoThumbnail;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    private function callPrivateMethod($class, string $method): \ReflectionMethod
    {
        $reflection = new \ReflectionClass($class);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
        return $method;
    }

    private function createVideoRecord(): Video
    {
        return factory(Video::class)->create();
    }

    private function createVideoThumbnailRecord(): VideoThumbnail
    {
        return factory(VideoThumbnail::class)->create();
    }

    private function createVideoAndVideoThumnailRecord(): array
    {
        $video = factory(Video::class, 1)
            ->create()
            ->each(function ($video) {
                factory(VideoThumbnail::class, 1)
                    ->make()
                    ->each(function ($video_thumbnail) use ($video) {
                        $video->video_thumbnail()->save($video_thumbnail);
                    });
            });
        $video_thumbnail = VideoThumbnail::where('video_id', $video[0]->id)->get();
        return [$video[0], $video_thumbnail[0]];
    }
}
