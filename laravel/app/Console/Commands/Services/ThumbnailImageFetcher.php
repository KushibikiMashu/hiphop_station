<?php

namespace App\Console\Commands\Services;

class ThumbnailImageFetcher
{

    private static $tableName;
    private static $thumbnailQuery;

    public function __construct($instance)
    {
        self::$tableName = $instance->getTable();
        self::$thumbnailQuery = $instance->get();
    }

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

    private function getImages($record, string $size): void
    {
        $table = $this->getTableName();
        $url = str_replace('_live', '', $record->{$size});
        $hash = substr(pathinfo($url)['dirname'], -11);
        $image_path = "image/{$table}/{$size}/{$hash}.jpg";
        if (file_exists(public_path($image_path))) return;

        // レスポンスコードが400系・500系でもwarningを出さない
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $data = file_get_contents($url, false, $context);
        if (strpos($http_response_header[0], '200') !== false) file_put_contents(public_path($image_path), $data);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return self::$tableName;
    }

    /**
     * @return array
     */
    public static function getThumbnailQuery(): object
    {
        return self::$thumbnailQuery;
    }
}
