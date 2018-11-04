<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\VideoThumbnail;

class FetchVideoThumbnailImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:videoThumbnail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $videoThumbnailQuery;
    protected $videoThumbnailTableName;

    /**
     * Create a new command instance.
     *
     * @param VideoThumbnail $videoThumbnail
     */
    public function __construct(VideoThumbnail $videoThumbnail)
    {
        parent::__construct();
        $this->videoThumbnailQuery = $videoThumbnail->get();
        $this->videoThumbnailTableName = $videoThumbnail->getTable();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // DBからstd, medium, highのアドレスを取得する
        // curl(Guzzle使う？)で画像を取得
        // publicディレクトリ配下、サイズごとに配置する
        // 画像名はhash.jpg
        $this->fetchThumbnailInDatabase($this->videoThumbnailQuery);

        // 将来的には...
        // S3に動画をアップ
        // S3のアドレスをjsonで管理
        // jsonをフロント（React）に渡す
    }

    public function fetchThumbnailInDatabase($data)
    {
        $sizes = ['std', 'medium', 'high'];
        foreach ($data as $record) {
            foreach ($sizes as $size) {
                $this->getImages($record, $size);
            }
        }
    }

    private function getImages($record, string $size)
    {
        $url = str_replace('_live', '', $record->{$size});
        $hash = substr(pathinfo($url)['dirname'], -11);
        $image_path = "image/{$this->videoThumbnailTableName}/{$size}/{$hash}.jpg";
        if (file_exists(public_path($image_path))) return;

        // レスポンスコードが400系・500系でもwarningを出さない
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $data = file_get_contents($url, false, $context);
        if (strpos($http_response_header[0], '200') !== false) file_put_contents(public_path($image_path), $data);
    }
}
