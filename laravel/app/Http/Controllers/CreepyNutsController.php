<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CreepyNutsController extends Controller
{
    public function display()
    {
        // $redis = new Predis\Client([
        //     'scheme' => 'tcp',
        //     'host'   => 'redis',
        //     'port'   => 6379,
        // ]);

        $results = [];
        
        // （String型）チャンネル　→ 歌手
        Redis::set("Creepy Nuts", "R-指定 & DJ 松永");
        var_dump(Redis::get("Creepy Nuts"));
        $results[] = Redis::get("Creepy Nuts");
        Redis::del("Creepy Nuts");

        // （List型）歌手 → 曲
        Redis::rPush("R-指定 & DJ 松永", "合法的トビ方ノススメ");
        Redis::rPush("R-指定 & DJ 松永", "助演男優賞");
        var_dump(Redis::lRange("R-指定 & DJ 松永", 0, 1));
        $results[] = Redis::lRange("R-指定 & DJ 松永", 0, 1);
        Redis::del("R-指定 & DJ 松永");

        // (Hash型)曲名 = [アドレス→'http', 日時→'2018']
        Redis::hset("合法的トビ方ノススメ", "adress", "https://www.youtube.com/watch?v=UVaZf3GliDs");
        Redis::hset("合法的トビ方ノススメ", "datetime", "2015-12-24");

        Redis::hset("助演男優賞", "adress", "https://www.youtube.com/watch?v=JEUOTHxL8Xw");
        Redis::hset("助演男優賞", "datetime", "2016-12-30");

        var_dump(Redis::hgetall("合法的トビ方ノススメ"));
        var_dump(Redis::hgetall("助演男優賞"));

        $results[] = Redis::hgetall("合法的トビ方ノススメ");
        $results[] = Redis::hgetall("助演男優賞");

        Redis::del("合法的トビ方ノススメ");
        Redis::del("助演男優賞");

        return view('redis', $results);
    }
}
