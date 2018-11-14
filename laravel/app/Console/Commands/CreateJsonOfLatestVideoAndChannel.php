<?php

namespace App\Console\Commands;

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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
        [$channels, $main] = $service->getArrays();
        $json = ['channels' => $channels, 'main' => $main];
        foreach ($json as $filename => $array) {
            $this->createJson($array, $filename);
        }
    }

    /**
     * JSONを作成する
     *
     * @param array $array
     * @param string $filename
     * @return void
     */
    private function createJson(array $array, string $filename)
    {
        $json = json_encode($array, JSON_UNESCAPED_UNICODE);
        $file = public_path( "json/{$filename}.json");
        file_put_contents($file, $json);
    }
}
