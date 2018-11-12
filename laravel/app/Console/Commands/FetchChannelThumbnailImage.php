<?php

namespace App\Console\Commands;

use App\Usecases\ChannelThumbnailFetcherUsecase;
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
     * @param ChannelThumbnailFetcherUsecase $usecase
     */
    public function handle(ChannelThumbnailFetcherUsecase $usecase)
    {
        $usecase->run();
    }
}
