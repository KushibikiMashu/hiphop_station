<?php

namespace App\Repositories;

use App\VideoThumbnail;
use Illuminate\Support\Facades\Log;

class VideoThumbnailRepository implements YoutubeThumbnailRepositoryInterface
{
    public function fetchAll(): \Generator
    {
        return VideoThumbnail::cursor();
    }

    public function getTableName(): string
    {
        return (new VideoThumbnail)->getTable();
    }

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
