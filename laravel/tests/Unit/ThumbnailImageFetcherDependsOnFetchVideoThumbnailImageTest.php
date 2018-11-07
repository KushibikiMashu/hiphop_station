<?php

namespace Tests\Unit;

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
    private $dependencyInstance;

    public function setUp()
    {
        parent::setup();
        $this->dependencyInstance = new VideoThumbnail;
        $this->instance = new ThumbnailImageFetcher($this->dependencyInstance);
    }

    public function test_テーブル名を取得する()
    {
        $this->assertSame('video_thumbnail', $this->instance::getTableName());
        $this->assertSame('video', $this->instance::getParentTableName());
        $this->assertSame(VideoThumbnail::get()[0]->high, $this->instance::getThumbnailQuery()[0]->high);
    }

    public function test_video_thumbnailテーブルのクエリの同一性を担保する()
    {
        $this->assertSame(VideoThumbnail::get()[0]->id, $this->instance::getThumbnailQuery()[0]->id);
        $this->assertSame(VideoThumbnail::get()[0]->video_id, $this->instance::getThumbnailQuery()[0]->video_id);
        $this->assertSame(VideoThumbnail::get()[0]->medium, $this->instance::getThumbnailQuery()[0]->medium);
    }

    public function test_videoテーブルのクエリの同一性を担保する()
    {
        $this->assertSame(Video::get()[0]->id, $this->instance::getParentTableQuery()[0]->id);
        $this->assertSame(Video::get()[0]->channel_id, $this->instance::getParentTableQuery()[0]->channel_id);
        $this->assertSame(Video::get()[0]->title, $this->instance::getParentTableQuery()[0]->title);
        $this->assertSame(Video::get()[0]->hash, $this->instance::getParentTableQuery()[0]->hash);
        $this->assertSame(Video::get()[0]->genre, $this->instance::getParentTableQuery()[0]->genre);
        $this->assertSame(Video::get()[0]->published_at, $this->instance::getParentTableQuery()[0]->published_at);
    }

    public function test_deleteInvalidRecord_YouTubeで削除済みの動画をDBから削除する()
    {
        // Fakerでデータ入れる
        $videoId = Video::insertGetId([
            'channel_id'   => 1000,
            'title'        => 'test',
            'hash'         => 'abcdefg',
            'genre'        => 'song',
            'published_at' => '2000-01-01T00:00:00.000Z',
        ]);

        $videoThumbnailId = VideoThumbnail::insertGetId([
            'video_id' => $videoId,
            'std'      => 'std',
            'medium'   => 'medium',
            'high'     => 'high',
        ]);
        $video = Video::where('id', '=', $videoId)->get();
        $videoThumbnail = VideoThumbnail::where('id', '=', $videoThumbnailId)->get();

        // データが挿入されたことを確認
        $this->assertSame(1000, $video[0]->channel_id);
        $this->assertSame($videoId, $videoThumbnail[0]->video_id);

        $method = $this->callPrivateMethod($this->instance, 'deleteInvalidRecord');
        $method->invoke($this->instance, 'video_thumbnail', $videoThumbnailId, 'abcdefg');

        $deletedVideo = Video::where('id', '=', $videoId)->get();
        $deletedVideoThumbnail = VideoThumbnail::where('id', '=', $videoThumbnailId)->get();
        $this->assertEmpty($deletedVideo);
        $this->assertEmpty($deletedVideoThumbnail);
    }

    public function callPrivateMethod($class, $method)
    {
        $reflection = new \ReflectionClass($class);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
        return $method;
    }
}
