<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerServices();
        $this->registerRepositories();
    }

    protected function registerServices()
    {
        $this->app->singleton(\App\Services\ThumbnailImageFetcher::class, function ($app) {
            return new \App\Services\ThumbnailImageFetcher($app);
        });
    }

    protected function registerRepositories()
    {
        $this->app->bind(
            \App\Repositories\YoutubeRepositoryInterface::Class,
            \App\Repositories\VideoRepository::class,
            \App\Repositories\ChannelRepository::class
        );

        $this->app->bind(
            \App\Repositories\YoutubeThumbnailRepositoryInterface::Class,
            \App\Repositories\VideoThumbnailRepository::class,
            \App\Repositories\ChannelThumbnailRepository::class
        );
    }
}
