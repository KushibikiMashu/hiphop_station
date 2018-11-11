<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\VideoRepository;
use App\Repositories\VideoThumbnailRepository;
use App\Repositories\DownloadJpgFileRepository;
use App\Services\VideoThumbnailFetcher;

class VideoThumbnailFetcherTest extends TestCase
{
    private $instance;

    public function setUp()
    {
        parent::setup();
        $video_repository = new VideoRepository;
        $video_thumbnail_repository = new VideoThumbnailRepository;
        $download_jpg_file_repository = new DownloadJpgFileRepository;
        $this->instance = new VideoThumbnailFetcher($video_repository, $video_thumbnail_repository, $download_jpg_file_repository);
    }

    /**
     * @test
     */
//    public function deleteInvalidRecord__video_thumbnailとそれに紐づくvideoのレコードをDBから削除する(): void
//    {
//        list($video, $video_thumbnail) = $this->createVideoAndVideoThumnailRecord();
//        $this->assertDatabaseHas('video', ['id' => $video->id]);
//        $this->assertDatabaseHas('video_thumbnail', ['id' => $video_thumbnail->id]);
//        $method = $this->callPrivateMethod($this->instance, 'deleteInvalidRecords');
//        $method->invoke($this->instance, $video_thumbnail->id, $video->hash);
//        $this->assertDatabaseMissing('video', ['id' => $video->id]);
//        $this->assertDatabaseMissing('video_thumbnail', ['id' => $video_thumbnail->id]);
//    }

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



}
