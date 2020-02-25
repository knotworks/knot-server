<?php

namespace Knot\Providers;

use Illuminate\Support\ServiceProvider;
use Knot\Services\MediaUploadService;

class MediaUploadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MediaUploadService::class, function () {
            return new MediaUploadService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
