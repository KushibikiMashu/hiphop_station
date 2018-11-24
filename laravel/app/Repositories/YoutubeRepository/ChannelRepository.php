<?php

namespace App\Repositories;

use App\Channel;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Mixed_;

class ChannelRepository implements YoutubeRepositoryInterface
{
    private $channel;

    public function __construct()
    {
        $this->channel = new Channel;
    }

    /**
     * channelテーブルの全レコードのジェネレータを取得する
     *
     * @return \Generator
     */
    public function fetchAll(): \Generator
    {
        return $this->channel->cursor();
    }

    /**
     * channelテーブルの全レコードを配列で取得する
     *
     * @return array
     */
    public function fetchAllAsArray(): array
    {
        return $this->channel->all()->toArray();
    }

    /**
     * channelテーブルの全レコードを降順の配列で取得する
     *
     * @return array
     */
    public function fetchAllOrderByPublishedAt(): array
    {
        return $this->channel->orderBy('published_at', 'desc')->get()->toArray();
    }

    /**
     * 引数のカラムを配列で取得する
     *
     * @param $column
     * @return array
     */
    public function fetchAnyColumn(string $column): array
    {
        return $this->channel->select($column)->get()->toArray();
    }

    /**
     * 引数に書かれているカラムをchannelテーブルから降順の配列で取得する
     *
     * @param mixed ...$columns
     * @return array
     */
    public function fetchAnyColumns(...$columns): array
    {
        if (empty($columns) || count($columns) === 1) {
            return null;
        }
        $query = $this->channel->select($columns[0]);
        $select_columns = array_slice($columns, 1);
        for ($i = 0; $i < count($select_columns); $i++) {
            $query->addSelect($select_columns[$i]);
        }
        return $query->orderBy('id', 'asc')->get()->toArray();
    }

    /**
     * テーブル名を取得する
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->channel->getTable();
    }

    /**
     * ChannelThumbnailのレコードに紐づくchannelのhashを取得する
     *
     * @param $record
     * @return string
     */
    public function getHashByChannelThumbnail($record): string
    {
        return $this->channel->where('id', $record->channel_id)->exists() ? $this->channel->find($record->channel_id)->hash : '';
    }

    /**
     * hashでchannelのレコードを削除する
     *
     * @param string $hash
     */
    public function deleteByHash(string $hash): void
    {
        $id = (string)$this->channel->where('hash', $hash)->get()[0]->id;
        if ($this->channel->where('id', $id)->exists()) {
            $this->channel->where('id', $id)->delete();
            Log::info("Delete id: {$id} from channel table\.");
        } else {
            Log::info("Cannot delete id {$id} from channel table\.");
        }
    }

    /**
     * channelのhashからidを取得する
     *
     * @param string $hash
     * @return int
     */
    public function fetchChannelIdByHash(string $hash): int
    {
        return $this->channel->where('hash', $hash)->first()->id;
    }

    /**
     * カラム名と値からchannelが登録済みであるかを確認する
     *
     * @param string $column
     * @param $value
     * @return bool
     */
    public function channel_exists(string $column, $value): bool
    {
        return $this->channel->where($column, $value)->exists();
    }

    /**
     * channelをDBに登録する
     *
     * @param array $record
     * @return Channel
     */
    public function saveRecord(array $record): Channel
    {
        return $this->channel->create($record);
    }

    /**
     * channel_idからchannelオブジェクトを取得する
     *
     * @param int $channel_id
     * @return Channel
     */
    public function fetchChannelByChannelId(int $channel_id): Channel
    {
        return $this->channel->find($channel_id);
    }

    /**
     * channelの動画数を更新する
     *
     * @param int $channel_id
     * @param int $video_count
     */
    public function updateVideoCount(int $channel_id, int $video_count): void
    {
        $this->channel->where('id', $channel_id)->update(['video_count' => $video_count]);
    }
}
