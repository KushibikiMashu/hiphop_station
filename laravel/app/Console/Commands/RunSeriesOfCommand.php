<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunSeriesOfCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run';

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
        \Artisan::call('fetch:newChannel');
        $this->info('finish fetch:newChannel');
        \Artisan::call('update:videoCount');
        $this->info('finish update:videoCount');
        \Artisan::call('fetch:allChannelThumbnail');
        $this->info('finish fetch:allChannelThumbnail');
        \Artisan::call('fetch:NewVideo');
        $this->info('finish fetch:NewVideo');
        \Artisan::call('fetch:allVideoThumbnail');
        $this->info('finish fetch:allVideoThumbnail');
        \Artisan::call('create:json');
        $this->info('finish create:json');
    }
}
