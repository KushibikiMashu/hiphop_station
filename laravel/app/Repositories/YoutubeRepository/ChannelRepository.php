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

    /**
     * ChannelThumbnailのレコードに紐づくchannelのhashを取得する
     *
     * @param $record
     * @return string
     */
    public function getHashFromChannelThumbnail($record): string
    {
        return Channel::where('id', $record->channel_id)->exists() ? Channel::find($record->channel_id)->hash : '';
    }

    /**
     * hashでchannelのレコードを削除する
     *
     * @param string $hash
     */
    public function deleteByHash(string $hash): void
    {
        $id = (string)Channel::where('hash', $hash)->get()[0]->id;
        if (Channel::where('id', $id)->exists()) {
            Channel::where('id', $id)->delete();
            Log::info("Delete id: {$id} from channel table\.");
        } else {
            Log::info("Cannot delete id {$id} from channel table\.");
        }
    }

}
