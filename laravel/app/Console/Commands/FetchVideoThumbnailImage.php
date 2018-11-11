<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Usecases\VideoThumbnailFetcherUsecase;

class FetchVideoThumbnailImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:videoThumbnail';

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
     * @param VideoThumbnailFetcherUsecase $usecase
     */
    public function handle(VideoThumbnailFetcherUsecase $usecase)
    {
        $usecase->run();
    }
}
