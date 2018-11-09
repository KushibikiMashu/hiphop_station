<?php

namespace App\Console\Commands;

use App\ChannelThumbnail;
use App\Services\ThumbnailImageFetcher;
use Illuminate\Console\Command;

class FetchChannelThumbnailImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:channelThumbnail';

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
     * @param ChannelThumbnail $channelThumbnail
     */
    public function handle(ChannelThumbnail $channelThumbnail)
    {
        (new ThumbnailImageFetcher($channelThumbnail))->downloadImages();
    }
}
