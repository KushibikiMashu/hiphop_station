<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExecuteUpdateVideoCountQuery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:videoCount';

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
     * @param \App\Services\ExecuteUpdateVideoCountQueryService $service
     */
    public function handle(\App\Services\ExecuteUpdateVideoCountQueryService $service)
    {
        $service->run();
    }
}
