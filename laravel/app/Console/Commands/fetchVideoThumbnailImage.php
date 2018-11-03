<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\Video;
use App\VideoThumbnail;

class fetchVideoThumbnailImage extends Command
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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->videoThumbnailQuery = VideoThumbnail::get();
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
        $data = VideoThumbnail::get();
        $this->fetchThumbnailInDatabase($data);

        // 将来的には...
        // S3に動画をアップ
        // S3のアドレスをjsonで管理
        // jsonをフロント（React）に渡す
    }

    public function fetchNewVideoThumbnail()
    {
        //
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
        $url = $record->{$size};
        $hash = substr(pathinfo($url)['dirname'], -11);
        $image_path = "image/video_thumbnail/{$size}/{$hash}.jpg";
        if (!file_exists(public_path($image_path))) {
            $path = public_path($image_path);
            $data = file_get_contents($url);
            if ($data) file_put_contents($path, $data);
        }
    }
}
