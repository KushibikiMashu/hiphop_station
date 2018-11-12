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

    /**
     * idでレコードを削除する
     *
     * @param int $id
     */
    public function deleteById(int $id): void
    {
        if (ChannelThumbnail::where('id', $id)->exists()) {
            ChannelThumbnail::where('id', $id)->delete();
            Log::info("Delete id: {$id} from channel_thumbnail table\.");
        } else {
            Log::info("Cannot delete id {$id} from channel_thumbnail table\.");
        }
    }
}
