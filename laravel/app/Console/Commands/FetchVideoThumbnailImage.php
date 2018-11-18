<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VideoThumbnailFetcherService;

class FetchVideoThumbnailImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:allVideoThumbnail';

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
     * @param VideoThumbnailFetcherService $service
     */
    public function handle(VideoThumbnailFetcherService $service)
    {
        $service->run();
    }
}
