<?php

namespace App\Repositories;

use App\ChannelThumbnail;
use Illuminate\Support\Facades\Log;

class ChannelThumbnailRepository implements YoutubeThumbnailRepositoryInterface
{
    private $channel_thumbnail;

    public function __construct()
    {
        $this->channel_thumbnail = new ChannelThumbnail;
    }

    /**
     * channel_thumbnailテーブルの全レコードのジェネレータを取得する
     *
     * @return \Generator
     */
    public function fetchAll(): \Generator
    {
        return $this->channel_thumbnail->cursor();
    }

    /**
     * テーブル名を取得する
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->channel_thumbnail->getTable();
    }

    /**
     * idでレコードを削除する
     *
     * @param int $id
     */
    public function deleteById(int $id): void
    {
        if ($this->channel_thumbnail->where('id', $id)->exists()) {
            $this->channel_thumbnail->where('id', $id)->delete();
            Log::info("Delete id: {$id} from channel_thumbnail table\.");
        } else {
            Log::info("Cannot delete id {$id} from channel_thumbnail table\.");
        }
    }

    /**
     * channel_thumbnailをDBに登録する
     *
     * @param array $record
     * @return ChannelThumbnail
     */
    public function saveRecord(array $record): ChannelThumbnail
    {
        return $this->channel_thumbnail->create($record);
    }

    /**
     * 5分前の間に登録されたサムネイルのレコードを取得する
     *
     * @param \Carbon\Carbon $five_minutes_ago
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function fetchRecordsOfOverTheLastFiveMinutes(\Carbon\Carbon $five_minutes_ago): \Illuminate\Database\Eloquent\Collection
    {
        $channel_thumbnails = $this->channel_thumbnail->all();
        return $channel_thumbnails->filter(function ($channel_thumbnail) use ($five_minutes_ago) {
            return $channel_thumbnail->created_at > $five_minutes_ago;
        });
    }
}
