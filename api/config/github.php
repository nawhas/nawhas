<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'private',

    /*
    |--------------------------------------------------------------------------
    | GitHub Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like. Note that the 6 supported authentication methods are:
    | "application", "jwt", "none", "password", "private", and "token".
    |
    */

    'connections' => [

        'main' => [
            'method'     => 'token',
            'token'      => env('GITHUB_TOKEN'),
            // 'backoff'    => false,
            // 'cache'      => false,
            // 'version'    => 'v3',
            // 'enterprise' => false,
        ],

        'app' => [
            'method'       => 'application',
            'clientId'     => 'your-client-id',
            'clientSecret' => 'your-client-secret',
            // 'backoff'      => false,
            // 'cache'        => false,
            // 'version'      => 'v3',
            // 'enterprise'   => false,
        ],

        'jwt' => [
            'method'       => 'jwt',
            'token'        => 'your-jwt-token',
            // 'backoff'      => false,
            // 'cache'        => false,
            // 'version'      => 'v3',
            // 'enterprise'   => false,
        ],

        'private' => [
            'method'          => 'private',
            'appId'           => env('GITHUB_APP_ID'),
            'keyPath'         => storage_path('secrets/github.pem'),
            'installation'    => env('GITHUB_INSTALLATION_ID'),
            // 'key'        => 'your-private-key-content',
            // 'backoff'    => false,
            // 'cache'      => false,
            // 'version'    => 'v3',
            // 'enterprise' => false,
        ],

        'password' => [
            'method'     => 'password',
            'username'   => 'your-username',
            'password'   => 'your-password',
            // 'backoff'    => false,
            // 'cache'      => false,
            // 'version'    => 'v3',
            // 'enterprise' => false,
        ],

        'none' => [
            'method'     => 'none',
            // 'backoff'    => false,
            // 'cache'      => false,
            // 'version'    => 'v3',
            // 'enterprise' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Cache
    |--------------------------------------------------------------------------
    |
    | Here are each of the cache configurations setup for your application.
    | Only the "illuminate" driver is provided out of the box. Example
    | configuration has been included.
    |
    */

    'cache' => [

        'main' => [
            'driver'    => 'illuminate',
            'connector' => null, // null means use default driver
            // 'min'       => 43200,
            // 'max'       => 172800
        ],

        'bar' => [
            'driver'    => 'illuminate',
            'connector' => 'redis', // config/cache.php
            // 'min'       => 43200,
            // 'max'       => 172800
        ],

    ],

    'enabled' => env('GITHUB_ENABLED', false),
];
