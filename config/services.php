<?php

declare(strict_types=1);

use App\Models\User;

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'model' => User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'telegram-bot-api' => [
        'token' => env('TELEGRAM_BOT_TOKEN'),
    ],

    'foursquare' => [
        'api_key' => env('FOURSQUARE_CLIENT_ID'),
        'api_secret' => env('FOURSQUARE_CLIENT_SECRET'),
    ],

    'opencage' => [
        'api_key' => env('OPENCAGE_API_KEY'),
    ],
];
