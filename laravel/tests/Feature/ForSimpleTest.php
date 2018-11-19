<?php
//
//namespace Tests\Feature;
//
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//use Illuminate\Foundation\Testing\RefreshDatabase;
//use App\Repositories\VideoRepository;
//use App\Repositories\VideoThumbnailRepository;
//use App\Console\Commands\FetchLatestVideosFromYoutubeApi;
//use Illuminate\Support\Facades\DB;
//use App\Video;
//
//class ForSimpleTest extends TestCase
//{
//    private $video_repo;
//    private $video_thumbnail_repo;
//    private $table = 'video';
//
//    public function setUp(): void
//    {
//        parent::setup();
//        $this->video_repo = new VideoRepository;
//        $this->video_thumbnail_repo = new VideoThumbnailRepository;
//    }
//
//    /**
//     * @test
//     */
//    public function simpleTest()
//    {
////        [$video, $video_thumbnail] = self::createVideoAndVideoThumbnailRecord();
//        $query = $this->video_thumbnail_repo->fetchAllOrderByAsArray('id');
//        $new_video_thumbnails = array_slice($query, 0, 2);
//        dump($new_video_thumbnails[0]['id']);
//        $this->assertTrue(True);
////        self::deleteRecordByTableAndId($video_thumbnail->getTable(), $video_thumbnail->id);
//        dd('end');
//    }
//
//}
