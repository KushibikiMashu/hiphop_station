<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\ChannelThumbnail;
use App\Repositories\ChannelThumbnailRepository;

class ChannelThumbnailRepositoryTest extends TestCase
{
    private $instance;
    private $table = 'channel_thumbnail';

    public function setUp()
    {
        parent::setUp();
        $this->instance = new ChannelThumbnailRepository;
    }

    /**
     * @test
     */
    public function fetchAll__Channelテーブルのレコードを全て取得する(): void
    {
        $expected = $actual = [];
        list($channel, $channel_thumbnail) = self::createChannelAndChannelThumbnailRecord();
        $generator = $this->instance->fetchAll();
        foreach (iterator_to_array($generator) as $record) {
            $actual[] = $record->getOriginal();
        }
        foreach (ChannelThumbnail::get() as $record) {
            $expected[] = $record->getOriginal();
        }
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($channel->getTable(), $channel->id);
        self::deleteRecordByTableAndId($channel_thumbnail->getTable(), $channel_thumbnail->id);
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
        [$channel, $channel_thumbnail] = self::createChannelAndChannelThumbnailRecord();
        $this->table = $channel_thumbnail->getTable();
        $id = $channel_thumbnail->id;
        $data = ['id' => $id];
        $this->assertDatabaseHas($this->table, $data);
        $this->instance->deleteById($id);
        $this->assertDatabaseMissing($this->table, $data);
        self::deleteRecordByTableAndId($channel->getTable(), $channel->id);
    }

    /**
     * @test
     */
    public function saveRecord__レコードを登録する(): void
    {
        $channel = self::createChannelRecord();
        $records = [
            'channel_id' => $channel->id,
            'std'      => $channel->hash,
            'medium'   => $channel->hash,
            'high'     => $channel->hash,
        ];
        $channel_thumbnail = $this->instance->saveRecord($records);
        $expected = $channel->id;
        $actual = $channel_thumbnail->channel_id;
        $this->assertSame($expected, $actual);
        self::deleteRecordByTableAndId($channel->getTable(), $channel->id);
        self::deleteRecordByTableAndId($channel_thumbnail->getTable(), $channel_thumbnail->id);
    }
}
