<?php

namespace App\Repositories;

use App\ChannelThumbnail;
use Illuminate\Support\Facades\Log;

class ChannelThumbnailRepository implements YoutubeThumbnailRepositoryInterface
{
    /**
     * channel_thumbnailテーブルの全レコードのジェネレータを取得する
     *
     * @return \Generator
     */
    public function fetchAll(): \Generator
    {
        return ChannelThumbnail::cursor();
    }

    /**
     * テーブル名を取得する
     *
     * @return string
     */
    public function getTableName(): string
    {
        return (new ChannelThumbnail)->getTable();
    }


}
