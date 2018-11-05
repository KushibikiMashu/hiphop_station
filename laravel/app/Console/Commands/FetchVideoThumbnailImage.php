<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Commands\Services\ThumbnailImageFetcher;
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

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param VideoThumbnail $videoThumbnail
     */
    public function handle(VideoThumbnail $videoThumbnail)
    {
        // DBからstd, medium, highのアドレスを取得する
        // curl(Guzzle使う？)で画像を取得
        // publicディレクトリ配下、サイズごとに配置する
        // 画像名はhash.jpg
        $fetcher = new ThumbnailImageFetcher($videoThumbnail);
        $fetcher->downloadImages();

        // 将来的には...
        // S3に動画をアップ
        // S3のアドレスをjsonで管理
        // jsonをフロント（React）に渡す
    }
}
