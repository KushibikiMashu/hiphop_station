<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\VideoThumbnail;
use App\Repositories\VideoThumbnailRepository;

class VideoThumbnailRepositoryTest extends TestCase
{
    private $instance;
    private $table = 'video_thumbnail';

    public function setUp(): void
    {
        parent::setup();
        $this->instance = new VideoThumbnailRepository;
    }

    /**
     * @test
     */
    public function fetchAll__Videoテーブルのレコードを全て取得する(): void
    {
        $expected = $actual = [];
        list($video, $video_thumbnail) = self::createVideoAndVideoThumbnailRecord();
        $generator = $this->instance->fetchAll();
        foreach (iterator_to_array($generator) as $record) {
            $actual[] = $record->getOriginal();
        }
        foreach (VideoThumbnail::get() as $record) {
            $expected[] = $record->getOriginal();
        }
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($video->getTable(), $video->id);
        self::deleteRecordByTableAndId($video_thumbnail->getTable(), $video_thumbnail->id);
    }

    /**
     * @test
     */
    public function getTable__テーブル名を取得する(): void
    {
        $expected = $this->instance->getTableName();
        $this->assertSame($this->table, $expected);
    }

    /**
     * @test
     */
    public function deleteById__idでレコードを削除する(): void
    {
        [$video, $video_thumbnail] = self::createVideoAndVideoThumbnailRecord();
        $this->table = $video_thumbnail->getTable();
        $id = $video_thumbnail->id;
        $data = ['id' => $id];
        $this->assertDatabaseHas($this->table, $data);
        $this->instance->deleteById($id);
        $this->assertDatabaseMissing($this->table, $data);
        self::deleteRecordByTableAndId($video->getTable(), $video->id);
    }
}
