<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use App\Repositories\ChannelRepository;

class CreateLatestJsonService
{
    private $video_repository;
    private $channel_repository;
    private static $uselessColumns = [
        'video' => ['id', 'video_count', 'published_at', 'created_at', 'updated_at'],
        'channel' => ['created_at', 'updated_at'],
    ];

    public function __construct(VideoRepository $video_repository, ChannelRepository $channel_repository)
    {
        $this->video_repository = $video_repository;
        $this->channel_repository = $channel_repository;
    }

    /**
     * video, channelの全レコードから、JSONに不要なレコードを削除する
     * →クエリビルダで作成可能では？（引数を必要なカラム名の配列とする関数をリポジトリに作る）
     * そうするとunsetKeys関数が不要になり、見通しが良くなる。
     * serviceでは必要なカラムを指定するだけで済む。ジェネレーターを取得した方が良さそう
     * ＝リファクタリング
     *
     * @return array
     */
    public function returnVideoAndChannelRecordArray(): array
    {
        $videos = $this->unsetKeys($this->video_repository->fetchAllOrderByPublishedAt(), self::$uselessColumns['video']);
        $channels = $this->unsetKeys($this->channel_repository->fetchAllAsArray(), self::$uselessColumns['channel']);
        return [$videos, $channels];
    }

    /**
     * 連想配列から第二引数のキー/値を削除する
     *
     * @param array $query
     * @param array $keys
     * @return array
     */
    private function unsetKeys(array $query, array $keys): array
    {
        $new_query = [];
        foreach ($query as $record) {
            foreach ($keys as $key) {
                unset($record[$key]);
            }
            $new_query[] = $record;
        }
        return $new_query;
    }
}
