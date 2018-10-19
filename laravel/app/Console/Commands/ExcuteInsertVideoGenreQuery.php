<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExcuteUpdateVideoGenreQuery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ExcuteUpdateVideoGenreQuery';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // DB::table('video')->update(
        //     ['genre' => 'song']
        // );

        // KOKをbattleに分類する
        DB::table('video')
            ->where('title', 'like', '%KOK%')
            ->orWhere('title', 'like', '%KING OF KINGS%')
            ->update(['genre' => 'battle']);

        // 戦国MCBattleとUMB、ifktvの特定の動画をbattleに分類する
        DB::table('video')
            ->where('channel_id', '=', '8')
            ->orWhere('channel_id', '=', '9')
            ->orWhere([
                    ['channel_id', '=', '23'],
                    ['title', 'like', '%SPOTLIGHT%']
                ])
            ->orWhere([
                    ['channel_id', '=', '23'],
                    ['title', 'like', '%ENTER%']
                ])
            ->update(['genre' => 'battle']);

        // 特定の条件の動画をsongに分類する
        DB::table('video')
            ->where('title', 'like', '%【MV】%')
            ->orWhere('title', 'like', '%Music Video%')
            ->orWhere('title', 'like', '%MusicVideo%')
            ->orWhere('title', 'like', '%CHECK YOUR MIC%')
            ->update(['genre' => 'song']);

        // genreをradio(Creepy Nuts)、interview(Neet Tokyoなど)、othersに分類する？
        // MVPではothersだけのリリースでOK
    }
}
