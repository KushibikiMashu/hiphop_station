<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateAddressOfStdVideoThumbnail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateAddressOfStdVideoThumbnail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * video_thumbnailテーブルにdefaultサイズの画像のアドレスを格納する
     *
     * @return mixed
     */
    public function handle()
    {
        $results = DB::table('video')
            ->select('hash')
            ->get();

        $id = 1;
        foreach($results as $result){

            DB::table('video_thumbnail')
                ->where('id', '=', $id)
                ->update(['std' => 'https://i.ytimg.com/vi/' . $result->hash . '/default.jpg']);

            $id++;
        }
    }
}
