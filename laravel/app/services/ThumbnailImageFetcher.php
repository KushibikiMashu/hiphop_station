<?php

namespace App\Services;

use App\VideoThumbnail;
use Illuminate\Support\Facades\Log;
use App\Video;
use App\Channel;
use App\Repositories\VideoRepository;
use App\Repositories\ChannelRepository;
use App\Repositories\VideoThumbnailRepository;
use App\Repositories\ChannelThumbnailRepository;
use App\Services\VideoThumbnailFetcherService;;

class ThumbnailImageFetcher
{

    private $instance;
    private $thumbnail_table_name; // おそらく不要
    private $thumbnail_query; // おそらく不要
    private $parent_table_name; // おそらく不要
    private $parent_table_query; // おそらく不要
    private $belonging_instance;
    private $repository;
    private $thumbnail_repository;

    /**
     * ThumbnailImageFetcher constructor.
     * @param $instance
     */
    public function __construct($instance)
    {
        $this->instance = $instance;
        $this->thumbnail_table_name = $instance->getTable();
        $this->thumbnail_query = $instance->get();
        $this->setBelongingInstance();
        $this->parent_table_name = $this->belonging_instance->getTable();
        $this->parent_table_query = $this->belonging_instance->get();
//        $this->setRepositories();
        // setterがいらない。getterもいらないと思う。このクラス外では呼ばない。呼ぶようになったらgetterを作る

        //        // 三項演算子でvideoかchannelかを判定し、プロパに格納する値を決める
        // instance of を使う
        // レポジトリの呼び出しはsetBelongingInstance（）と同じ実装方式で行う
    }

    /**
     * XXX_thumbnailテーブルに格納されているアドレスの画像をダウンロードする
     */
//    public function downloadImages(): void
//    {
//        $sizes = ['std', 'medium', 'high'];
//        $query = $this->getThumbnailQuery();
//        // これだけで済む
//        // $query = $this->instance->get();
//        foreach ((new VideoThumbnailRepository())->fetchAll() as $record) {
//            foreach ($sizes as $size) {
//                $this->fetchThumbnailInDatabase($record, $size);
//            }
//        }
//    }

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
        if (!$hash = $this->fetchRecordHash($record)) return;
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
        if ($this->belonging_instance->where('id', $record->{$parent_id})->exists()) {
            return $this->belonging_instance
                ->where('id', $record->{$parent_id})
                ->get()[0]->hash;
        }
        return '';
    }

    /**
     * YouTubeから削除された動画のIDをDBから削除する
     *
     * @param VideoThumbnailFetcherService $service
     * @param int $id
     * @param string $hash
     */
    private function deleteInvalidRecord(VideoThumbnailFetcherService $service, int $id, string $hash): void
    {

        $service->run($id, $hash);

//        $table = $this->getTableName();
//        $parent_table = $this->getParentTableName();
////        if ($this->belonging_instance->where('hash', $hash)->exists()) {
////            $this->belonging_instance->where('hash', $hash)->delete();
////            Log::info('Delete id: ' . (string)$this->instance::where('id', $id)->get()[0]->id . " from {$parent_table} table.");
////        }
////        if ($this->instance->where('id', $id)->exists()) {
////            $this->instance->where('id', $id)->delete();
////            Log::info('Delete id: ' . $id . " from {$table} table.");
////        }
    }

    public function setBelongingInstance(): void
    {
        $this->belonging_instance = $this->instance instanceof VideoThumbnail ? new Video : new Channel;
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

    public function setRepositories()
    {
        $this->repository =  new VideoRepository();
    }
}
