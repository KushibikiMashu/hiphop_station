<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ChangeDataTypeOfPublishedAt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:publishedAt';

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
        $channels = \App\Channel::all();
        foreach ($channels as $channel) {
            $channel->published_at = (new \Carbon\Carbon($channel->published_at_original))->format('Y-m-d H:i:s');
            $channel->save();
            $this->info($channel->id);
        }
        $this->info('Channel変更完了');

        $videos = \App\Video::all();
        foreach ($videos as $video) {
            $video->published_at = (new \Carbon\Carbon($video->published_at_original))->format('Y-m-d H:i:s');
            $video->save();
            $this->info($video->id);
        }
        $this->info('Video変更完了');
    }
}
