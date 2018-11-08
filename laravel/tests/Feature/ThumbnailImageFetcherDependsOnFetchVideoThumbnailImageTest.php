<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Video;
use App\VideoThumbnail;
use App\Console\Commands\FetchVideoThumbnailImage;

use App\Console\Commands\Services\ThumbnailImageFetcher;

class ThumbnailImageFetcherDependsOnFetchVideoThumbnailImageTest extends TestCase
{
    /**
     * @var ThumbnailImageFetcher instance
     */
    private $instance;

    public function setUp()
    {
        parent::setup();
        $this->instance = new ThumbnailImageFetcher(new VideoThumbnail);
    }

    /**
     * @test
     */
    public function テーブル名を取得する(): void
    {
        $this->assertSame('video_thumbnail', $this->instance::getTableName());
        $this->assertSame('video', $this->instance::getParentTableName());
    }

    /**
     * @test
     */
    public function video_thumbnailテーブルのクエリの同一性を担保する(): void
    {
        $video_thumbnail = VideoThumbnail::get()[0];
        $this->assertSame($video_thumbnail->id, $this->instance::getThumbnailQuery()[0]->id);
        $this->assertSame($video_thumbnail->video_id, $this->instance::getThumbnailQuery()[0]->video_id);
        $this->assertSame($video_thumbnail->medium, $this->instance::getThumbnailQuery()[0]->medium);
        $this->assertSame($video_thumbnail->high, $this->instance::getThumbnailQuery()[0]->high);
    }

    /**
     * @test
     */
    public function videoテーブルのクエリの同一性を担保する(): void
    {
        $video = Video::get()[0];
        $this->assertSame($video->id, $this->instance::getParentTableQuery()[0]->id);
        $this->assertSame($video->channel_id, $this->instance::getParentTableQuery()[0]->channel_id);
        $this->assertSame($video->title, $this->instance::getParentTableQuery()[0]->title);
        $this->assertSame($video->hash, $this->instance::getParentTableQuery()[0]->hash);
        $this->assertSame($video->genre, $this->instance::getParentTableQuery()[0]->genre);
        $this->assertSame($video->published_at, $this->instance::getParentTableQuery()[0]->published_at);
    }

    /**
     * @test
     */
    public function deleteInvalidRecord_YouTubeで削除済みの動画をDBから削除する(): void
    {
        list($video, $video_thumbnail) = $this->createVideoAndVideoThumnailRecord();
        $this->assertDatabaseHas('video', ['id' => $video->id]);
        $this->assertDatabaseHas('video_thumbnail', ['id' => $video_thumbnail->id]);
        $method = $this->callPrivateMethod($this->instance, 'deleteInvalidRecord');
        $method->invoke($this->instance, $video_thumbnail->getTable(), $video_thumbnail->id, $video->hash);
        $this->assertDatabaseMissing('video', ['id' => $video->id]);
        $this->assertDatabaseMissing('video_thumbnail', ['id' => $video_thumbnail->id]);
    }
//
//    /**
//     * @test
//     */
//    public function RDBで親テーブルのレコードを取得()
//    {
//        $video_Thumbnail = VideoThumbnail::find(1);
//        $method = $this->callPrivateMethod($this->instance, 'fetchRecordHash');
//        $method->invoke($this->instance, $video_Thumbnail);
//    }

    private function callPrivateMethod(ThumbnailImageFetcher $class, string $method): \ReflectionMethod
    {
        $reflection = new \ReflectionClass($class);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
        return $method;
    }

    private function createVideoRecord()
    {
        return factory(Video::class)->create();
    }

    private function createVideoThumbnailRecord()
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
