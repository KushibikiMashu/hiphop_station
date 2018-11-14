<?php

namespace App\Console\Commands;

use App\Channel;
use App\Video;
use App\Services\CreateLatestJsonService;
use Illuminate\Console\Command;

class CreateJsonOfLatestVideoAndChannel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new json file referring latest records of Channel and Video tables';

    /**
     * channelテーブルの全レコード
     * videoテーブルの全レコード
     *
     * @var array
     * @var array
     */
    private $channel_query;
    private $video_query_orderby_published_at;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->channel_query = Channel::all();
        $this->video_query_orderby_published_at = Video::orderBy('published_at', 'desc')->get();
        // $this->>usecase = $usecase;
    }

    /**
     * (方針)オブジェクト指向。疎結合。関数を短く記述する
     * 再利用できるモジュール。モジュールは関数型のように。
     * 参照透過性。副作用なし。
     *
     * @param CreateLatestJsonService $service
     */
    public function handle(CreateLatestJsonService $service)
    {
        $service->returnAssocArrays();

        // 配列を受け取り、json作成
//        [$channels, $main] = $service->returnAssocArrays();
//        $json = [
//            'channels' => $channels,
//            'main' => $main,
//        ];
//        foreach ($json as $filename => $array) {
//            $this->createJson($array, $filename);
//        }



//        $channels = $this->unset_keys($this->channel_query->toArray(), ['id', 'video_count', 'published_at', 'created_at', 'updated_at']);
//        $video_query = $this->unset_keys($this->video_query_orderby_published_at->toArray(), ['created_at', 'updated_at']);
//        $main = $this->add_extra_data($video_query, $channels);
//
//        $queries = ['channels' => $channels, 'main' => $main];
//        foreach ($queries as $filename => $query) {
//            $this->create_json($query, $filename);
//        }

        // $this->usecase->run();
    }
//
//    /**
//     * JSONを作成する
//     *
//     * @param array $array
//     * @param string $filename
//     * @return void
//     */
//    private function create_json(array $array, string $filename)
//    {
//        $json = json_encode($array, JSON_UNESCAPED_UNICODE);
//        $file = dirname(__FILE__) . "/../../../public/json/{$filename}.json";
//        file_put_contents($file, $json);
//    }
//
//    /**
//     * 連想配列からkeyがcreated_at,update_atであるキー/値を削除する
//     *
//     * @param array $query
//     * @param array $keys
//     * @return array
//     */
//    private function unset_keys(array $query, array $keys): array
//    {
//        $new_query = [];
//        foreach ($query as $record) {
//            foreach ($keys as $key) {
//                unset($record[$key]);
//            }
//            $new_query[] = $record;
//        }
//        return $new_query;
//    }
//
//    /**
//     * 動画に紐づくchannel情報とサムネイルのURLを追加する
//     *
//     * @param $video_query
//     * @param array $channels
//     * @return array
//     */
//    private function add_extra_data($video_query, array $channels): array
//    {
//        $new_query = [];
//        $sizes = ['std', 'medium', 'high'];
//        foreach ($video_query as $record) {
//            $record['channel'] = $channels[$record['channel_id'] - 1];
//            $record['thumbnail'] = 'https://i.ytimg.com/vi/' . $record['hash'] . '/hqdefault.jpg';
//            $record['thumbnail'] = [
//                'std'    => "/image/video_thumbnail/$sizes[0]/{$record['hash']}.jpg",
//                'medium' => "/image/video_thumbnail/$sizes[1]/{$record['hash']}.jpg",
//                'high'   => "/image/video_thumbnail/$sizes[2]/{$record['hash']}.jpg"
//            ];
//            $new_query[] = $record;
//        }
//        return $new_query;
//    }
}
