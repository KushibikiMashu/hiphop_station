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
    public function getTable__テーブル名を取得する(): void
    {
        $expected = $this->instance->getTableName();
        $this->assertSame($this->table, $expected);
    }
}
