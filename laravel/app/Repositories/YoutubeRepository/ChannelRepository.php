<?php

namespace App\Repositories;

use App\Channel;
use Illuminate\Support\Facades\Log;

class ChannelRepository implements YoutubeRepositoryInterface
{
    /**
     * channelテーブルの全レコードのジェネレータを取得する
     *
     * @return \Generator
     */
    public function fetchAll(): \Generator
    {
        return Channel::cursor();
    }

    /**
     * テーブル名を取得する
     *
     * @return string
     */
    public function getTableName(): string
    {
        return (new Channel)->getTable();
    }
}
