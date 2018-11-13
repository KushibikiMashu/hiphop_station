<?php

namespace App\Usecases;

use App\Services\CreateLatestJsonService;

class CreateLatestJsonUsecase
{
    private $service;

    public function __construct(CreateLatestJsonService $service)
    {
        $this->service = $service;
    }

    public function run() :void
    {
        // 返却される値を使う
        // その他のデータを付け加える
        // JSONを作成する
        [$videos, $channels] = $this->service->returnVideoAndChannelRecordArray();
        $main = $this->addExtraData($videos, $channels);
        foreach (['channels' => $channels, 'main' => $main] as $filename => $query) {
            $this->createJson($query, $filename);
        }
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
     * JSONを作成する
     *
     * @param array $array
     * @param string $filename
     * @return void
     */
    private function createJson(array $array, string $filename): void
    {
        $json = json_encode($array, JSON_UNESCAPED_UNICODE);
        // パスはpublic_pathで書き換える
        $file = dirname(__FILE__) . "/../../../public/json/{$filename}.json";
        file_put_contents($file, $json);
    }
}
