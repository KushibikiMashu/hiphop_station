<?php

namespace App\Repositories;

use App\Video;
use Illuminate\Support\Facades\Log;

class VideoRepository implements YoutubeRepositoryInterface
{
    public function fetchAll(): \Generator
    {
        return Video::cursor();
    }

    public function getTableName(): string
    {
        return (new Video)->getTable();
    }

    /**
     * RDBで親テーブルのレコードを取得
     *
     * @param $record
     * @return string
     */
    public function getHashFromVideoThumbnail($record): string
    {
        return Video::where('id', $record->video_id)->exists() ? Video::find($record->video_id)->hash : '';
    }

    /**
     * @param string $hash
     */
    public function deleteByHash(string $hash): void
    {
        $id = (string)Video::where('hash', $hash)->get()[0]->id;
        if (Video::where('id', $id)->exists()) {
            Video::where('id', $id)->delete();
            Log::info("Delete id: {$id} from video table\.");
        }
    }
}
