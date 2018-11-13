<?php

namespace App\Repositories;

use App\Video;
use Illuminate\Support\Facades\Log;

class VideoRepository implements YoutubeRepositoryInterface
{
    /**
     * videoテーブルの全レコードのジェネレータを取得する
     *
     * @return \Generator
     */
    public function fetchAll(): \Generator
    {
        return Video::cursor();
    }

    /**
     * videoテーブルの全レコードのジェネレータを降順の配列で取得する
     *
     * @return array
     */
    public function fetchAllOrderByPublishedAt(): array
    {
        return Video::orderBy('published_at', 'desc')->get()->toArray();
    }

    /**
     * テーブル名を取得する
     *
     * @return string
     */
    public function getTableName(): string
    {
        return (new Video)->getTable();
    }

    /**
     * VideoThumbnailのレコードに紐づくvideoのhashを取得する
     *
     * @param $record
     * @return string
     */
    public function getHashFromVideoThumbnail($record): string
    {
        return Video::where('id', $record->video_id)->exists() ? Video::find($record->video_id)->hash : '';
    }

    /**
     * hashでvideoのレコードを削除する
     *
     * @param string $hash
     */
    public function deleteByHash(string $hash): void
    {
        $id = (string)Video::where('hash', $hash)->get()[0]->id;
        if (Video::where('id', $id)->exists()) {
            Video::where('id', $id)->delete();
            Log::info("Delete id: {$id} from video table\.");
        } else {
            Log::info("Cannot delete id {$id} from video table\.");
        }
    }
}
