<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Video;
use App\VideoThumbnail;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function callPrivateMethod($class, string $method): \ReflectionMethod
    {
        $reflection = new \ReflectionClass($class);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
        return $method;
    }

    protected function createVideoRecord(): Video
    {
        return factory(Video::class)->create();
    }

    protected function createVideoAndVideoThumnailRecord(): array
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

    protected function deleteVideoRecordById(string $table, int $id): void
    {
        DB::table($table)->where('id', $id)->delete();
    }
}
