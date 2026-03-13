<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\CurrentLocationService;
use App\Contracts\LinkMetaService;
use App\Contracts\NearbyService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NearbyService::class, config('app.nearby_service_class'));
        $this->app->bind(CurrentLocationService::class, config('app.current_location_service_class'));
        $this->app->bind(LinkMetaService::class, config('app.link_meta_service_class'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Factory::guessFactoryNamesUsing(static function (string $modelName): string {
            return 'Database\\Factories\\' . class_basename($modelName) . 'Factory';
        });
    }
}
