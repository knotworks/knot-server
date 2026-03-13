<?php

declare(strict_types=1);

use App\Services\FoursquareNearbyService;
use App\Services\OpenCageCurrentLocationService;
use App\Services\XPathLinkMetaService;

return [

    'name' => env('APP_NAME', 'Knot'),

    'admin_email' => env('ADMIN_EMAIL'),

    'posts_per_page' => 10,

    'disable_new_signups' => env('DISABLE_NEW_SIGNUPS', false),

    'nearby_service_class' => FoursquareNearbyService::class,

    'current_location_service_class' => OpenCageCurrentLocationService::class,

    'link_meta_service_class' => XPathLinkMetaService::class,

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'https://knot.test'),

    'timezone' => 'UTC',

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],
];
