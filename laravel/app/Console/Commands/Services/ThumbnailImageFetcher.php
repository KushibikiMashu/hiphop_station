<?php

namespace App\Console\Commands\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThumbnailImageFetcher
{
    
    private $instance;
    private $thumbnail_table_name;
    private $thumbnail_query;
    private $parent_table_name;
    private $parent_table_query;

    /**
     * ThumbnailImageFetcher constructor.
     * @param $instance
     */
    public function __construct($instance)
    {
        // 三項演算子でvideoかchannelかを判定し、プロパティに格納する値を決める
        // instance of を使う
        $this->instance = $instance;
        $this->thumbnail_table_name = $instance->getTable();
        $this->setParentTableName();
        $this->thumbnail_query = $instance->get();
        $this->setParentTableQuery();
    }

    /**
     * XXX_thumbnailテーブルに格納されているアドレスの画像をダウンロードする
     */
    public function downloadImages(): void
    {
        $sizes = ['std', 'medium', 'high'];
        $query = $this->getThumbnailQuery();
        // これだけで済む
        // $query = $this->instance->get();
        foreach ($query as $record) {
            foreach ($sizes as $size) {
                $this->fetchThumbnailInDatabase($record, $size);
            }
        }
    }

    /**
     * file_get_contentsで画像を取得する
     *
     * @param object $record
     * @param string $size
     */
    private function fetchThumbnailInDatabase($record, string $size): void
    {
        $table = $this->getTableName();
        $url = str_replace('_live', '', $record->{$size});
        $hash = $this->fetchRecordHash($record);
        if (!$hash) return;
        $image_path = "image/{$table}/{$size}/{$hash}.jpg";
        if (file_exists(public_path($image_path))) return;

        $result = $this->canDownloadJpgFileFromUrl($url, $image_path);
        if (!$result) {
            Log::warning('Cannot download image file from: ' . $url);
            $this->deleteInvalidRecord($record->id, $hash);
        }
    }

    private function canDownloadJpgFileFromUrl($url, $image_path): bool
    {
        // レスポンスコードが400系・500系でもwarningを出さない
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $data = file_get_contents($url, false, $context);
        if (strpos($http_response_header[0], '200') !== false) {
            file_put_contents(public_path($image_path), $data);
            return true;
        }
        return false;
    }

    /**
     * RDBで親テーブルのレコードを取得
     *
     * @param $record
     * @return string
     */
    private function fetchRecordHash($record): string
    {
        $parent_table = $this->getParentTableName();
        $parent_id = $parent_table . '_id';
        if (DB::table($parent_table)->where('id', $record->{$parent_id})->exists()) {
            return DB::table($parent_table)
                ->where('id', $record->{$parent_id})
                ->get()[0]->hash;
        }
        return '';
    }

    /**
     * YouTubeから削除された動画のIDをDBから削除する
     *
     * @param int $id
     * @param string $hash
     */
    private function deleteInvalidRecord(int $id, string $hash): void
    {
        $table = $this->getTableName();
        $parent_table = $this->getParentTableName();
        if (DB::table($parent_table)->where('hash', $hash)->exists()) {
            DB::table($parent_table)->where('hash', $hash)->delete();
            Log::info('Delete id: ' . (string)$this->instance::where('id', $id)->get()[0]->id . " from {$parent_table} table.");
        }
        if (DB::table($table)->where('id', $id)->exists()) {
            DB::table($table)->where('id', $id)->delete();
            Log::info('Delete id: ' . $id . " from {$table} table.");
        }
    }

    public function setParentTableName(): void
    {
        $this->parent_table_name = str_replace('_thumbnail', '', $this->thumbnail_table_name);
    }

    public function setParentTableQuery(): void
    {
        $this->parent_table_query = DB::table($this->getParentTableName())
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->thumbnail_table_name;
    }

    /**
     * @return string
     */
    public function getParentTableName(): string
    {
        return $this->parent_table_name;
    }

    /**
     * @return object
     */
    public function getThumbnailQuery(): object
    {
        return $this->thumbnail_query;
    }

    /**
     * @return object
     */
    public function getParentTableQuery(): object
    {
        return $this->parent_table_query;
    }
}
