<?php

namespace Tests\Unit;

use Tests\TestCase;
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
        $video = self::createVideoRecord();
        $generator = $this->instance->fetchAll();
        foreach (iterator_to_array($generator) as $record) {
            $actual[] = $record->getOriginal();
        }
        foreach (Video::get() as $record) {
            $expected[] = $record->getOriginal();
        }
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($video->getTable(), $video->id);
    }

    /**
     * @test
     */
    public function fetchAllOrderByPublishedAt__Videoテーブルのレコードを全て取得する(): void
    {
        $expected = $actual = [];
        $video = self::createVideoRecord();
        $actual = $this->instance->fetchAllOrderByPublishedAt();
        foreach (Video::orderBy('published_at', 'desc')->get() as $record) {
            $expected[] = $record->getOriginal();
        }
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($video->getTable(), $video->id);
    }

    /**
     * @test
     */
    public function fetchColumnsOrderByPublishedAt__カラムの指定が0個なのでnullを返す()
    {
        $actual = $this->instance->fetchColumnsOrderByPublishedAt([]);
        $this->assertSame([], $actual);
    }

    /**
     * @test
     */
    public function fetchColumnsOrderByPublishedAt__カラムの指定が1個なのでnullを返す()
    {
        $actual = $this->instance->fetchColumnsOrderByPublishedAt('id');
        $this->assertSame([], $actual);
    }

    /**
     * @test
     */
    public function fetchColumnsOrderByPublishedAt__カラムを指定してpublished_atで降順にし、配列で返す()
    {
        $video = self::createVideoRecord();
        $expected = Video::orderBy('published_at', 'desc')->get()->toArray();
        $actual = $this->instance->fetchColumnsOrderByPublishedAt('id', 'channel_id', 'title', 'hash', 'genre', 'published_at', 'created_at', 'updated_at');
        $this->assertInternalType('array', $actual);
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($video->getTable(), $video->id);
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
        [$video, $video_thumbnail] = self::createVideoAndVideoThumbnailRecord();
        $actual = $this->instance->getHashFromVideoThumbnail($video_thumbnail);
        $this->assertSame($video->hash, $actual);
        self::deleteRecordByTableAndId($video->getTable(), $video->id);
        self::deleteRecordByTableAndId($video_thumbnail->getTable(), $video_thumbnail->id);
    }

    /**
     * @test
     */
    public function getHashFromVideoThumbnail__idを受け取り、データが存在しない場合は空文字を返す(): void
    {
        [$video, $video_thumbnail] = self::createVideoAndVideoThumbnailRecord();
        self::deleteRecordByTableAndId($video->getTable(), $video->id);
        $actual = $this->instance->getHashFromVideoThumbnail($video_thumbnail);
        $this->assertSame('', $actual);
        self::deleteRecordByTableAndId($video_thumbnail->getTable(), $video_thumbnail->id);
    }

    /**
     * @test
     */
    public function deleteByHash__hashでレコードを削除する(): void
    {
        $video = self::createVideoRecord();
        $this->table = $video->getTable();
        $hash = $video->hash;
        $data = ['hash' => $hash];
        $this->assertDatabaseHas($this->table, $data);
        $this->instance->deleteByHash($hash);
        $this->assertDatabaseMissing($this->table, $data);
    }

    /**
     * @test
     */
    public function fetchLatestPublishedVideoRecord__最新のpublished_atのレコードを取得する(): void
    {
        $video = self::createVideoRecord();
        $actual = $this->instance->fetchLatestPublishedVideoRecord()->published_at;
        $expected = $video->published_at;
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($video->getTable(), $video->id);
    }
}
