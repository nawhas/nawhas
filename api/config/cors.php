<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://localhost:8080',
        'https://' . env('APP_DOMAIN'),
        'https://*.' . env('APP_DOMAIN'),
        'https://' . env('APP_DOMAIN') . ':8080',
        'https://*.' . env('APP_DOMAIN') . ':8080',
        'https://*.ngrok.io',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 60 * 60 * 24,

    'supports_credentials' => true,

];
