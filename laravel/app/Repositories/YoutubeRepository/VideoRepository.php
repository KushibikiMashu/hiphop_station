<?php

namespace App\Repositories;

use App\Video;
use Illuminate\Support\Facades\Log;

class VideoRepository implements YoutubeRepositoryInterface
{
    private $video;

    public function __construct()
    {
        $this->video = new Video;
    }

    /**
     * videoテーブルの全レコードのジェネレータを取得する
     *
     * @return \Generator
     */
    public function fetchAll(): \Generator
    {
        return $this->video->cursor();
    }

    /**
     * 引数のカラムを配列で取得する
     *
     * @param $column
     * @return array
     */
    public function fetchAnyColumn(string $column): array
    {
        return $this->video->select($column)->get()->toArray();
    }

    /**
     * videoテーブルの全レコードを降順の配列で取得する
     *
     * @return array
     */
    public function fetchAllOrderByPublishedAt(): array
    {
        return $this->video->orderBy('published_at', 'desc')->get()->toArray();
    }

    /**
     * 引数に書かれているカラムをvideoテーブルからアップロード日の降順の配列で取得する
     *
     * @param mixed ...$columns
     * @return array
     */
    public function fetchColumnsOrderByPublishedAt(...$columns): array
    {
        if (empty($columns) || count($columns) === 1) {
            return [];
        }
        $query          = $this->video->select($columns[0]);
        $select_columns = array_slice($columns, 1);
        for ($i = 0; $i < count($select_columns); $i++) {
            $query->addSelect($select_columns[$i]);
        }
        return $query->orderBy('published_at', 'desc')->get()->toArray();
    }

    /**
     * テーブル名を取得する
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->video->getTable();
    }

    /**
     * VideoThumbnailのレコードに紐づくvideoのhashを取得する
     *
     * @param $record
     * @return string
     */
    public function getHashFromVideoThumbnail($record): string
    {
        return $this->video->where('id', $record->video_id)->exists() ? $this->video->find($record->video_id)->hash : '';
    }

    /**
     * hashでvideoのレコードを削除する
     *
     * @param string $hash
     */
    public function deleteByHash(string $hash): void
    {
        $id = (string)$this->video->where('hash', $hash)->get()[0]->id;
        if ($this->video->where('id', $id)->exists()) {
            $this->video->where('id', $id)->delete();
            Log::info("Delete id: {$id} from video table\.");
        } else {
            Log::info("Cannot delete id {$id} from video table\.");
        }
    }

    /**
     * published_atが最新であるレコードを取得する
     *
     * @return Video
     */
    public function fetchLatestPublishedAtVideoRecord(): Video
    {
        $query = $this->video->select('id', 'published_at')->get()->toArray();
        foreach ($query as $key => $value) {
            $query[$key]['published_at'] = strtotime($value['published_at']);
        }
        array_multisort(array_column($query, 'published_at'), SORT_DESC, $query);
        return $this->video->find(array_shift($query)['id']);
    }

    /**
     * 一つのカラムの値を全て取得する
     *
     * @param string $column
     * @return \Illuminate\Support\Collection
     */
    public function fetchPluckedColumn($column): \Illuminate\Support\Collection
    {
        return $this->video->pluck($column);
    }

    /**
     * videoをDBに登録する
     *
     * @param array $record
     * @return Video
     */
    public function saveRecord(array $record): Video
    {
        return $this->video->create($record);
    }

    /**
     * videoのhashからidを取得する
     *
     * @param string $hash
     * @return int
     */
    public function fetchVideoIdByHash(string $hash): int
    {
        return $this->video->where('hash', $hash)->first()->id;
    }

    /**
     * channelに紐づくvideoが存在するかをチェックする
     *
     * @param $channel_id
     * @return bool
     */
    public function channelVideoExists($channel_id): bool
    {
        return $this->video->where('channel_id', $channel_id)->exists();
    }

    /**
     * channelに紐づくDB内の動画数を取得する
     *
     * @param int $channel_id
     * @return int
     */
    public function countVideoByChannelId(int $channel_id): int
    {
        return $this->video->where('channel_id', $channel_id)->count();
    }

    /**
     * published_atが１週間以内の動画を取得する
     *
     * @return array
     */
    public function getVideosOfThisWeek(): array
    {
        $videos = $this->video->all();
        return $videos->filter(function ($video) {
            $published_at = new \Carbon\Carbon($video->published_at);
            $oneWeekAgo   = (new \Carbon\Carbon())->subWeek();
            return $published_at->gte($oneWeekAgo);
        })->toArray();
    }

    /**
     * channelテーブルとvideoテーブルをjoinしたレコードを取得する
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllVideoJoinedChannelTwoWeeks(): \Illuminate\Support\Collection
    {
        return $this->video
            ->select(
                'video.id as id',
                'video.title as title',
                'video.hash as hash',
                'video.genre as genre',
                'video.published_at as published_at',
                'video.created_at as created_at',
                'channel.id as channel_id',
                'channel.title as channel_title',
                'channel.hash as channel_hash',
                'channel.published_at as channel_published_at',
                'channel.created_at as channel_created_at'
                )
            ->join('channel', 'video.channel_id', '=', 'channel.id')

            ->where('video.created_at', '>', (new \Carbon\Carbon)->subWeeks(2)->format('Y-m-d H:i:s'))
            ->orderBy('video.created_at', 'desc')
            ->get();
    }
}
