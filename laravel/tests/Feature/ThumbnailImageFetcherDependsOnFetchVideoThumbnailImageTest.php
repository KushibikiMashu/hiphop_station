<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Video;
use App\VideoThumbnail;
//use App\Services\VideoThumbnailFetcher;
//use App\Repositories\VideoRepository;
//use App\Repositories\VideoThumbnailRepository;

use App\Repositories\VideoRepository;
use App\Repositories\ChannelRepository;
use App\Repositories\VideoThumbnailRepository;
use App\Repositories\ChannelThumbnailRepository;
use App\Services\VideoThumbnailFetcher;

class ThumbnailImageFetcherDependsOnFetchVideoThumbnailImageTest extends TestCase
{
    private $instance;

    public function setUp()
    {
        parent::setup();
        $video_repository = new VideoRepository;
        $video_thumbnail_repository = new VideoThumbnailRepository;
        $this->instance = new VideoThumbnailFetcher($video_repository, $video_thumbnail_repository);
    }

    /**
     * @test
     */
    public function deleteInvalidRecord__video_thumbnailとそれに紐づくvideoのレコードをDBから削除する(): void
    {
        list($video, $video_thumbnail) = $this->createVideoAndVideoThumnailRecord();
        $this->assertDatabaseHas('video', ['id' => $video->id]);
        $this->assertDatabaseHas('video_thumbnail', ['id' => $video_thumbnail->id]);
        $method = $this->callPrivateMethod($this->instance, 'deleteInvalidRecords');
        $method->invoke($this->instance, $video_thumbnail->id, $video->hash);
        $this->assertDatabaseMissing('video', ['id' => $video->id]);
        $this->assertDatabaseMissing('video_thumbnail', ['id' => $video_thumbnail->id]);
    }

//    /**
//     * @test
//     */
//    public function fetchRecordHash__RDBで親テーブルのレコードを取得(): void
//    {
//        list($video, $video_thumbnail) = $this->createVideoAndVideoThumnailRecord();
//        $method = $this->callPrivateMethod($this->instance, 'fetchRecordHash');
//        $hash = $method->invoke($this->instance, $video_thumbnail);
//        $this->assertSame($video->hash, $hash);
//    }

    private function callPrivateMethod(VideoThumbnailFetcher $class, string $method): \ReflectionMethod
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
