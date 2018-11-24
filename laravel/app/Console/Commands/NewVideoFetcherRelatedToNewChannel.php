<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewVideoFetcherService;

class NewVideoFetcherRelatedToNewChannel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Fetch:NewVideo';

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
     * @param NewVideoFetcherService $service
     */
    public function handle(NewVideoFetcherService $service)
    {
        $service->run();
        \Log::info("OK 'Fetch:NewVideo'");
    }
}
