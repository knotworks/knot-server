<?php

namespace Knot\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

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
    }
}
