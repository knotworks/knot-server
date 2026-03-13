<?php

declare(strict_types=1);

return [

    'paths' => [
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'register',
        'api/*',
        'forgot-password',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
