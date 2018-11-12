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
}
