<?php

namespace App\Console\Commands\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThumbnailImageFetcher
{

    private static $thumbnailTableName;
    private static $thumbnailQuery;
    private static $parent_tableName;
    private static $parent_tableQuery;

    /**
     * ThumbnailImageFetcher constructor.
     * @param $instance
     */
    public function __construct($instance)
    {
        self::setTableName($instance);
        self::setParentTableName($instance);
        self::$thumbnailQuery = $instance->get();
        self::$parent_tableQuery = DB::table(self::getParentTableName())
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
     * file_get_contentsで画像を取得する
     *
     * @param object $record
     * @param string $size
     */
    private function getImages($record, string $size): void
    {
        $table = self::getTableName();
        $url = str_replace('_live', '', $record->{$size});
        $hash = $this->fetchRecordHash($record);
        $image_path = "image/{$table}/{$size}/{$hash}.jpg";
        if (file_exists(public_path($image_path))) return;

        // レスポンスコードが400系・500系でもwarningを出さない
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $data = file_get_contents($url, false, $context);
        if (strpos($http_response_header[0], '200') !== false) {
            file_put_contents(public_path($image_path), $data);
        } else {
            Log::warning('Cannot download image file from: ' . $url);
            $this->deleteInvalidRecord($table, $record->id, $hash);
        }
    }

    /**
     * RDBで親テーブルのレコードを取得
     *
     * @param $record
     * @return string
     */
    private function fetchRecordHash($record): string
    {
        $parent_table = self::getParentTableName();
        $parent_id = $parent_table . '_id';
        return DB::table($parent_table)
            ->where('id', '=', $record->{$parent_id})
            ->get()[0]->hash;
    }

    /**
     * YouTubeから削除された動画のIDをDBから削除する
     *
     * @param string $table
     * @param int $id
     * @param string $hash
     */
    private function deleteInvalidRecord(string $table, int $id, string $hash): void
    {
        $parent_table = self::getParentTableName();
        if (DB::table($parent_table)->where('hash', '=', $hash)->exists()) {
            DB::table($parent_table)->where('hash', '=', $hash)->delete();
            Log::info('Delete id: ' . self::$parent_tableQuery[$id - 1]->id . " from {$parent_table} table.");
        }
        if (DB::table($table)->where('id', '=', $id)->exists()) {
            DB::table($table)->where('id', '=', $id)->delete();
            Log::info('Delete id: ' . $id . " from {$table} table.");
        }
    }

    public static function setTableName($instance): void
    {
        self::$thumbnailTableName = $instance->getTable();
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return self::$thumbnailTableName;
    }

    public static function setParentTableName($instance): void
    {
        self::$parent_tableName = str_replace('_thumbnail', '', $instance->getTable());
    }

    /**
     * @return string
     */
    public static function getParentTableName(): string
    {
        return self::$parent_tableName;
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
        return self::$parent_tableQuery;
    }
}
