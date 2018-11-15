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
     * videoテーブルの全レコードを降順の配列で取得する
     *
     * @return array
     */
    public function fetchAllOrderByPublishedAt(): array
    {
        return $this->video->orderBy('published_at', 'desc')->get()->toArray();
    }

    /**
     * 引数に書かれているカラムをvideoテーブルから降順の配列で取得する
     *
     * @param mixed ...$columns
     * @return array
     */
    public function fetchColumnsOrderByPublishedAt(...$columns): array
    {
        if (empty($columns) || count($columns) === 1) {
            return [];
        }
        $query = $this->video->select($columns[0]);
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
}
