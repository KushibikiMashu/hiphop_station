<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Channel;
use App\Repositories\ChannelRepository;

class ChannelRepositoryTest extends TestCase
{
    private $instance;
    private $table = 'channel';

    public function setUp()
    {
        parent::setUp();
        $this->instance = new ChannelRepository;
    }

    /**
     * @test
     */
    public function fetchAll__Channelテーブルのレコードを全て取得する(): void
    {
        $expected = $actual = [];
        $channel = self::createChannelRecord();
        $generator = $this->instance->fetchAll();
        foreach (iterator_to_array($generator) as $record) {
            $actual[] = $record->getOriginal();
        }
        foreach (Channel::get() as $record) {
            $expected[] = $record->getOriginal();
        }
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($channel->getTable(), $channel->id);
    }

    /**
     * @test
     */
    public function fetchAllAsArray__Channelテーブルのレコードを配列で全て取得する(): void
    {
        $expected = $actual = [];
        $channel = self::createChannelRecord();
        $actual = $this->instance->fetchAllAsArray();
        foreach (Channel::get() as $record) {
            $expected[] = $record->getOriginal();
        }
        $this->assertInternalType('array', $actual);
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($channel->getTable(), $channel->id);
    }

    /**
     * @test
     */
    public function fetchAnyColumn__任意のカラムを１つ取得する(): void
    {
        $channel = self::createChannelRecord();
        $expected = [];
        foreach (Channel::pluck('id') as $id) {
            $expected[]['id'] = $id;
        }
        $actual = $this->instance->fetchAnyColumn('id');
        $this->assertInternalType('array', $actual);
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($channel->getTable(), $channel->id);
    }

    /**
     * @test
     */
    public function fetchAnyColumns__任意のカラムを複数取得する(): void
    {
        $channel = self::createChannelRecord();
        $expected = Channel::orderBy('id', 'asc')->get()->toArray();
        $actual = $this->instance->fetchAnyColumns('id', 'title', 'hash', 'video_count', 'published_at', 'created_at', 'updated_at');
        $this->assertInternalType('array', $actual);
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($channel->getTable(), $channel->id);
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
    public function getHashFromChannelThumbnail__idを受け取り、データが存在する場合はhashを返す(): void
    {
        [$channel, $channel_thumbnail] = self::createChannelAndChannelThumbnailRecord();
        $actual = $this->instance->getHashFromChannelThumbnail($channel_thumbnail);
        $this->assertSame($channel->hash, $actual);
        self::deleteRecordByTableAndId($channel->getTable(), $channel->id);
        self::deleteRecordByTableAndId($channel_thumbnail->getTable(), $channel_thumbnail->id);
    }

    /**
     * @test
     */
    public function getHashFromChannelThumbnail__idを受け取り、データが存在しない場合は空文字を返す(): void
    {
        [$channel, $channel_thumbnail] = self::createChannelAndChannelThumbnailRecord();
        self::deleteRecordByTableAndId($channel->getTable(), $channel->id);
        $actual = $this->instance->getHashFromChannelThumbnail($channel_thumbnail);
        $this->assertSame('', $actual);
        self::deleteRecordByTableAndId($channel_thumbnail->getTable(), $channel_thumbnail->id);
    }

    /**
     * @test
     */
    public function deleteByHash__hashでレコードを削除する(): void
    {
        $record = self::createChannelRecord();
        $this->table = $record->getTable();
        $hash = $record->hash;
        $data = ['hash' => $hash];
        $this->assertDatabaseHas($this->table, $data);
        $this->instance->deleteByHash($hash);
        $this->assertDatabaseMissing($this->table, $data);
    }

    /**
     * @test
     */
    public function fetchChannelIdByHash__channelのhashからidを取得する(): void
    {
        $channel = self::createChannelRecord();
        $expected = Channel::where('hash', $channel->hash)->get()[0]->id;
        $actual = $this->instance->fetchChannelIdByHash($channel->hash);
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($channel->getTable(), $channel->id);
    }

    /**
     * @test
     */
    public function channel_exists__カラム名と値からchannelが登録済みであるかを確認する(): void
    {
        $channel = self::createChannelRecord();
        $actual = $this->instance->channel_exists('id', $channel->id);
        $this->assertTrue($actual);
        self::deleteRecordByTableAndId($channel->getTable(), $channel->id);
    }

    /**
     * @test
     */
    public function saveRecord__レコードを登録する(): void
    {
        $record = [
            'title'        => 'test',
            'hash'         => str_random(24),
            'video_count'        => rand(100, 500),
            'published_at' => '2018-01-01 00:00:00'
        ];
        $channel = $this->instance->saveRecord($record);
        $this->assertDatabaseHas('channel', ['id' => $channel['id']]);
        self::deleteRecordByTableAndId($channel->getTable(), $channel->id);
    }
}
