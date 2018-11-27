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
    public function fetchAnyColumn__任意のカラムを１つ取得する(): void
    {
        $video = self::createVideoRecord();
        $expected = [];
        foreach (Video::pluck('id') as $id) {
            $expected[]['id'] = $id;
        }
        $actual = $this->instance->fetchAnyColumn('id');
        $this->assertInternalType('array', $actual);
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
    public function fetchLatestPublishedAtVideoRecord__最新のpublished_atのレコードを取得する(): void
    {
        $video = self::createVideoRecord();
        $expected = $video->published_at;
        $actual = $this->instance->fetchLatestPublishedAtVideoRecord()->published_at;
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($video->getTable(), $video->id);
    }

    /**
     * @test
     */
    public function fetchPluckedColumn__カラムの値だけを抽出する(): void
    {
        $video = self::createVideoRecord();
        $query = Video::select('id')->get()->toArray();
        $expected = [];
        foreach ($query as $ids) {
            $expected[] = $ids['id'];
        }
        $actual = $this->instance->fetchPluckedColumn('id')->toArray();
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($video->getTable(), $video->id);
    }

    /**
     * @test
     */
    public function saveRecord__レコードを登録する(): void
    {
        $channel = self::createChannelRecord();
        $record = [
            'channel_id'   => $channel->id,
            'title'        => 'test',
            'hash'         => str_random(11),
            'genre'        => 'song',
            'published_at' => '2018-11-18 13:12:57'
        ];
        $video = $this->instance->saveRecord($record);
        $expected = $channel->id;
        $actual = $video->channel_id;
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($channel->getTable(), $channel->id);
        self::deleteRecordByTableAndId($video->getTable(), $video->id);
    }

    /**
     * @test
     */
    public function fetchVideoIdByHash__channelのhashからidを取得する(): void
    {
        $video = self::createVideoRecord();
        $expected = Video::where('hash', $video->hash)->get()[0]->id;
        $actual = $this->instance->fetchVideoIdByHash($video->hash);
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($video->getTable(), $video->id);
    }

    /**
     * @test
     */
    public function channelVideoExists__channelに紐づくvideoが存在するかをチェックする(): void
    {
        $channel = self::createChannelRecord();
        $actual = $this->instance->channelVideoExists($channel->id);
        $this->assertFalse($actual);
        self::deleteRecordByTableAndId($channel->getTable(), $channel->id);
    }

    /**
     * @test
     */
    public function countVideoByChannelId__channelに紐づくDB内の動画数を取得する(): void
    {
        $channel = self::createChannelRecord();
        $actual = $this->instance->countVideoByChannelId($channel->id);
        $this->assertSame(0, $actual);
        self::deleteRecordByTableAndId($channel->getTable(), $channel->id);
    }

    /**
     * @test
     */
    public function getVideosOfThisWeek__１週間以内の動画を取得する():void
    {
        $video = self::createVideoRecord();
        $actual = $this->instance->getVideosOfThisWeek();
        $actual_array = [];
        foreach ($actual as $video) {
            $actual_array[] = $video->getOriginal();
        }
        $this->assertContains($video->getOriginal(), $actual_array);
        self::deleteRecordByTableAndId($video->getTable(), $video->id);
    }
}
