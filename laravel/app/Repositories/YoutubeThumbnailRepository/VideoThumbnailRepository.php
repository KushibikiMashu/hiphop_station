<?php

namespace App\Repositories;

use App\VideoThumbnail;
use Illuminate\Support\Facades\Log;

class VideoThumbnailRepository implements YoutubeThumbnailRepositoryInterface
{
    /**
     * video_thumbnailテーブルの全レコードのジェネレータを取得する
     *
     * @return \Generator
     */
    public function fetchAll(): \Generator
    {
        return VideoThumbnail::cursor();
    }

    /**
     * テーブル名を取得する
     *
     * @return string
     */
    public function getTableName(): string
    {
        return (new VideoThumbnail)->getTable();
    }

    /**
     * idでレコードを削除する
     *
     * @param int $id
     */
    public function deleteById(int $id): void
    {
        if (VideoThumbnail::where('id', $id)->exists()) {
            VideoThumbnail::where('id', $id)->delete();
            Log::info("Delete id: {$id} from video_thumbnail table\.");
        } else {
            Log::info("Cannot delete id {$id} from video_thumbnail table\.");
        }
    }
}
