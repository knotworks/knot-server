<?php

namespace Knot\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Knot\Contracts\CurrentLocationService;
use Knot\Contracts\LinkMetaService;
use Knot\Contracts\NearbyService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Factory::guessFactoryNamesUsing(function ($modelName) {
            return 'Database\\Factories\\'.class_basename($modelName).'Factory';
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(NearbyService::class, config('app.nearby_service_class'));
        $this->app->bind(CurrentLocationService::class, config('app.current_location_service_class'));
        $this->app->bind(LinkMetaService::class, config('app.link_meta_service_class'));
    }
}
