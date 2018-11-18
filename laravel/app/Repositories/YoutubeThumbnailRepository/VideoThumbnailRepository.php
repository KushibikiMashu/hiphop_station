<?php

namespace App\Repositories;

use App\VideoThumbnail;
use Illuminate\Support\Facades\Log;

class VideoThumbnailRepository implements YoutubeThumbnailRepositoryInterface
{
    private $video_thumbnail;

    public function __construct()
    {
        $this->video_thumbnail = new VideoThumbnail;
    }

    /**
     * video_thumbnailテーブルの全レコードのジェネレータを取得する
     *
     * @return \Generator
     */
    public function fetchAll(): \Generator
    {
        return $this->video_thumbnail->cursor();
    }

    /**
     * テーブル名を取得する
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->video_thumbnail->getTable();
    }

    /**
     * idでレコードを削除する
     *
     * @param int $id
     */
    public function deleteById(int $id): void
    {
        if ($this->video_thumbnail->where('id', $id)->exists()) {
            $this->video_thumbnail->where('id', $id)->delete();
            Log::info("Delete id: {$id} from video_thumbnail table\.");
        } else {
            Log::info("Cannot delete id {$id} from video_thumbnail table\.");
        }
    }

    /**
     * video_thumbnailをDBに登録する
     *
     * @param array $record
     * @return VideoThumbnail
     */
    public function saveRecord(array $record): VideoThumbnail
    {
        return $this->video_thumbnail->create($record);
    }
}
