<?php

namespace App\Console\Commands;

use App\Services\ChannelThumbnailFetcherService;
use Illuminate\Console\Command;

class FetchChannelThumbnailImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:allChannelThumbnail';

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
     * @param ChannelThumbnailFetcherService $service
     */
    public function handle(ChannelThumbnailFetcherService $service)
    {
        $service->run();
    }
}
