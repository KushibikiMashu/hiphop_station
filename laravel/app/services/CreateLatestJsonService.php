<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use App\Repositories\ChannelRepository;

class CreateLatestJsonService
{
    private $video_repository;
    private $channel_repository;
    private static $uselessColumns = [
        'channel' => ['video_count', 'published_at', 'created_at', 'updated_at'],
        'video' => ['created_at', 'updated_at'],
    ];

    public function __construct(VideoRepository $video_repository, ChannelRepository $channel_repository)
    {
        $this->video_repository = $video_repository;
        $this->channel_repository = $channel_repository;
    }

    public function getArrays() :array
    {
        $videos = $this->video_repository->fetchColumnsOrderByPublishedAt('id', 'channel_id', 'title', 'hash', 'genre', 'published_at');
        $channels = $this->channel_repository->fetchColumnsOrderById('id', 'title', 'hash');
        $main = $this->addExtraData($videos, $channels);
        return [$channels, $main];
    }

    /**
     * 動画に紐づくchannel情報とサムネイルのURLを追加する
     *
     * @param $videos
     * @param array $channels
     * @return array
     */
    private function addExtraData($videos, array $channels): array
    {
        $new_query = [];
        $sizes = ['std', 'medium', 'high'];
        foreach ($videos as $record) {
            dd($record);
            $record['channel'] = $channels[$record['channel_id'] - 1]; // これだけ別の関数に出す
            $record['thumbnail'] = 'https://i.ytimg.com/vi/' . $record['hash'] . '/hqdefault.jpg';
            $record['thumbnail'] = [
                'std'    => "/image/video_thumbnail/$sizes[0]/{$record['hash']}.jpg",
                'medium' => "/image/video_thumbnail/$sizes[1]/{$record['hash']}.jpg",
                'high'   => "/image/video_thumbnail/$sizes[2]/{$record['hash']}.jpg"
            ];
            $new_query[] = $record;
        }
        return $new_query;
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
    public function getVideoAndChannelRecordArray(): array
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