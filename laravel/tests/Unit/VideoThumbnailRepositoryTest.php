<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\VideoThumbnail;
use App\Repositories\VideoThumbnailRepository;

class VideoThumbnailRepositoryTest extends TestCase
{
    private $video_thumbnail_repository;
    private $table = 'video_thumbnail';

    public function setUp(): void
    {
        parent::setup();
        $this->video_thumbnail_repository = new VideoThumbnailRepository;
    }

    /**
     * @test
     */
    public function fetchAll__Videoテーブルのレコードを全て取得する(): void
    {
        $expected = $actual = [];
        $generator = $this->video_thumbnail_repository->fetchAll();
        foreach (iterator_to_array($generator) as $record) {
            $actual[] = $record->getOriginal();
        }
        foreach (VideoThumbnail::get() as $record) {
            $expected[] = $record->getOriginal();
        }
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getTable__テーブル名を取得する(): void
    {
        $expected = $this->video_thumbnail_repository->getTableName();
        $this->assertSame($this->table, $expected);
    }

    /**
     * @test
     */
    public function deleteById__idでレコードを削除する(): void
    {
        list($video, $video_thumbnail) = $this->createVideoAndVideoThumnailRecord();
        $this->table = $video_thumbnail->getTable();
        $id = $video_thumbnail->id;
        $data = ['id' => $id];
        $this->assertDatabaseHas($this->table, $data);
        $this->video_thumbnail_repository->deleteById($id);
        $this->assertDatabaseMissing($this->table, $data);
        $this->deleteVideoRecordById($video->getTable(), $video->id);
    }
}
