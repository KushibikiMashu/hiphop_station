<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Video;
use App\Repositories\VideoRepository;

class VideoRepositoryTest extends TestCase
{
    private $instance;
    private $table = 'video';

    public function setUp(): void
    {
        parent::setup();
        $this->instance = new VideoRepository;
    }

    /**
     * @test
     */
    public function fetchAll__Videoテーブルのレコードを全て取得する(): void
    {
        $expected = $actual = [];
        $video = $this->createVideoRecord();
        $generator = $this->instance->fetchAll();
        foreach (iterator_to_array($generator) as $record) {
            $actual[] = $record->getOriginal();
        }
        foreach (Video::get() as $record) {
            $expected[] = $record->getOriginal();
        }
        $this->assertSame($expected, $actual);
        $this->deleteRecordByTableAndId($video->getTable(), $video->id);
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
    public function getHashFromVideoThumbnail__idを受け取り、データが存在する場合はhashを返す(): void
    {
        list($video, $video_thumbnail) = $this->createVideoAndVideoThumnailRecord();
        $actual = $this->instance->getHashFromVideoThumbnail($video_thumbnail);
        $this->assertSame($video->hash, $actual);
        $this->deleteRecordByTableAndId($video->getTable(), $video->id);
        $this->deleteRecordByTableAndId($video_thumbnail->getTable(), $video_thumbnail->id);
    }

    /**
     * @test
     */
    public function getHashFromVideoThumbnail__idを受け取り、データが存在しない場合は空文字を返す(): void
    {
        list($video, $video_thumbnail) = $this->createVideoAndVideoThumnailRecord();
        $this->deleteRecordByTableAndId($video->getTable(), $video->id);
        $actual = $this->instance->getHashFromVideoThumbnail($video_thumbnail);
        $this->assertSame('', $actual);
        $this->deleteRecordByTableAndId($video_thumbnail->getTable(), $video_thumbnail->id);
    }

    /**
     * @test
     */
    public function deleteByHash__hashでレコードを削除する(): void
    {
        $record = $this->createVideoRecord();
        $this->table = $record->getTable();
        $hash = $record->hash;
        $data = ['hash' => $hash];
        $this->assertDatabaseHas($this->table, $data);
        $this->instance->deleteByHash($hash);
        $this->assertDatabaseMissing($this->table, $data);
    }
}
