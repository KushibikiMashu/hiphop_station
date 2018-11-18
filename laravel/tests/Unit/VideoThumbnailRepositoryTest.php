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
        [$video, $video_thumbnail] = self::createVideoAndVideoThumbnailRecord();
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
    public function fetchAllOrderBy__指定したカラムの降順で全てのレコードを取得する(): void
    {
        $expected = [];
        $query = VideoThumbnail::orderBy('id', 'desc')->get();
        foreach ($query as $record) {
            $expected[] = $record;
        }
        $actual = $this->instance->fetchAllOrderByAsArray('id');
        $this->assertSame(gettype($expected), gettype($actual));
        $this->assertSame(count($expected), count($actual));
        $this->assertSame(get_class($expected[0]), get_class($actual[0]));
        $this->assertSame($expected[0]->getOriginal(), $actual[0]->getOriginal());
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

    /**
     * @test
     */
    public function saveRecord__レコードを登録する(): void
    {
        $video = self::createVideoRecord();
        $records = [
            'video_id' => $video->id,
            'std'      => $video->hash,
            'medium'   => $video->hash,
            'high'     => $video->hash,
        ];
        $video_thumbnail = $this->instance->saveRecord($records);
        $expected = $video->id;
        $actual = $video_thumbnail->video_id;
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($video->getTable(), $video->id);
        self::deleteRecordByTableAndId($video_thumbnail->getTable(), $video_thumbnail->id);
    }
}
