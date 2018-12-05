<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\ApiRepository;

class UpdateVideoGenre extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:genre';

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
     * @param ApiRepository $api_repo
     */
    public function handle(ApiRepository $api_repo)
    {
        $videos = \App\Video::all();
        foreach($videos as $video) {
            $channel = $video->channel;
            $genre = $api_repo->getGenre($channel->id, $channel->hash, $video->title);
            if ($genre === $video->genre) continue;
            \App\Video::find($video->id)->update(['genre' => $genre]);
        }
    }
}
