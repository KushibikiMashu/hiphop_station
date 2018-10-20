<?php

namespace App\Console\Commands;

use App\Channel;
use App\Video;
use Illuminate\Console\Command;

class CreateJsonOfLatestVideoAndChannel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:newJson';

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
        $this->channel_query = Channel::all()->toArray();
        $this->video_query_orderby_published_at = Video::orderBy('published_at', 'desc')->get()->toArray();
    }

    /**
     * (方針)オブジェクト指向。疎結合。関数を短く記述する
     * 再利用できるモジュール。モジュールは関数型のように。
     * 参照透過性。副作用なし。
     */
    public function handle()
    {
        $channels = $this->channel_query;
        $main = $this->video_query_orderby_published_at;

        $queries = ['channels' => $channels, 'main' => $main];
        foreach ($queries as $filename => $query) {
            $this->create_json($this->unset_datetime_keys($query), $filename);
        }
    }

    /**
     * JSONを作成する
     *
     * @param array $array
     * @param string $file_name
     * @return void
     */
    private function create_json(array $array, string $filename)
    {
        $json = json_encode($array, JSON_UNESCAPED_UNICODE);
        $file = dirname(__FILE__) . "/../../../public/json/{$filename}.json";
        file_put_contents($file, $json);
    }

    /**
     * 連想配列からkeyがcreated_at,update_atであるキー/値を削除する
     *
     * @param array $query
     * @return array
     */
    private function unset_datetime_keys(array $query): array
    {
        $new_query = [];
        foreach ($query as $record) {
            unset($record['created_at'], $record['updated_at']);
            $new_query[] = $record;
        }
        return $new_query;
    }
}
