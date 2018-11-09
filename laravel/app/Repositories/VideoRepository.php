<?php

namespace App\Repositories;

use App\Video;
use Illuminate\Support\Facades\Log;

class VideoRepository implements YoutubeRepositoryInterface
{
    public function getTableName(): string
    {
    }

    public function deleteByHash(string $hash): void
    {
        $id = (string)Video::where('hash', $hash)->get()[0]->id;
        if (Video::where('id', $id)->exists()) {
            Video::where('id', $id)->delete();
            Log::info("Delete id: {$id} from video table\.");
        }
    }
}
