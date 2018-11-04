<?php

namespace App\Console\Commands\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThumbnailImageFetcher
{

    private static $ThumbnailTableName;
    private static $thumbnailQuery;
    private static $parentTableQuery;

    /**
     * ThumbnailImageFetcher constructor.
     * @param $instance
     */
    public function __construct($instance)
    {
        self::$ThumbnailTableName = $instance->getTable();
        self::$thumbnailQuery = $instance->get();
        self::$parentTableQuery = DB::table(str_replace('_thumbnail', '', self::$ThumbnailTableName))
            ->select('id', 'hash')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * XXX_thumbnailテーブルに格納されているアドレスの画像をダウンロードする
     */
    public function fetchThumbnailInDatabase(): void
    {
        $sizes = ['std', 'medium', 'high'];
        $query = $this->getThumbnailQuery();
        foreach ($query as $record) {
            foreach ($sizes as $size) {
                $this->getImages($record, $size);
            }
        }
    }

    /**
     *
     *
     * @param object $record
     * @param string $size
     */
    private function getImages($record, string $size): void
    {
        $table = $this->getTableName();
        $url = str_replace('_live', '', $record->{$size});
        $hash = self::$parentTableQuery[$record->id -1]->hash;
        $image_path = "image/{$table}/{$size}/{$hash}.jpg";
        if (file_exists(public_path($image_path))) return;

        // レスポンスコードが400系・500系でもwarningを出さない
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $data = file_get_contents($url, false, $context);
        if (strpos($http_response_header[0], '200') !== false) {
            file_put_contents(public_path($image_path), $data);
        } else {
            Log::warning('Cannot download image file from: ' . $url);
        }
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return self::$ThumbnailTableName;
    }

    /**
     * @return object
     */
    public static function getThumbnailQuery(): object
    {
        return self::$thumbnailQuery;
    }

    /**
     * @return object
     */
    public static function getParentTableQuery(): object
    {
        return self::$parentTableQuery;
    }
}
