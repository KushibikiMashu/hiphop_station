<?php

namespace App\Console\Commands;

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
     * (方針)オブジェクト指向。疎結合
     * 再利用できるモジュール。モジュールは関数型のように。
     * 参照透過性。副作用なし。
     *
     * @return mixed
     */
    public function handle()
    {
        // 作成するjsonはmain, battle, song, channel, channelごとのvideo（工夫次第で不要）
        // video [channel_id => '', title => '', hash => '', genre => '', published_at => '']
        // channel 
        // song, battle, channelごとのjsonが不要になる方法
        // video video.channel_id === 23なら、video.hashを使用する
        // video video.genre === songなら、video.hashを使用する
        // video video.genre === battleなら、video.hashを使用する

        // 設計
        // DBから値を読み取る
        // published_atで昇順に並び替える
        // 上記の連想配列を作成
        // jsonにencodeする
        // public/json配下に出力する
        // これをvideoとchannelで行う

    }
}
