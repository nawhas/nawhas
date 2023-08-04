<?php

/**
 * Sentry Laravel SDK configuration file.
 *
 * @see https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/
 */
return [

    // @see https://docs.sentry.io/product/sentry-basics/dsn-explainer/
    'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),

    // The release version of your application
    // Example with dynamic git hash: trim(exec('git --git-dir ' . base_path('.git') . ' log --pretty="%h" -n1 HEAD'))
    'release' => env('GIT_SHA'),

    // When left empty or `null` the Laravel environment will be used (usually discovered from `APP_ENV` in your `.env`)
    'environment' => null,

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#sample-rate
    'sample_rate' => (float)env('SENTRY_SAMPLE_RATE', 1.0),

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#traces-sample-rate
    'traces_sample_rate' => (float)env('SENTRY_TRACES_SAMPLE_RATE', 1.0),

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#profiles-sample-rate
    'profiles_sample_rate' => env('SENTRY_PROFILES_SAMPLE_RATE') === null
        ? null
        : (float)env(
            'SENTRY_PROFILES_SAMPLE_RATE'
        ),

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#send-default-pii
    'send_default_pii' => true,

    // Breadcrumb specific configuration
    'breadcrumbs' => [
        // Capture Laravel logs as breadcrumbs
        'logs' => true,

        // Capture Laravel cache events (hits, writes etc.) as breadcrumbs
        'cache' => true,

        // Capture Livewire components like routes as breadcrumbs
        'livewire' => false,

        // Capture SQL queries as breadcrumbs
        'sql_queries' => true,

        // Capture SQL query bindings (parameters) in SQL query breadcrumbs
        'sql_bindings' => true,

        // Capture queue job information as breadcrumbs
        'queue_info' => true,

        // Capture command information as breadcrumbs
        'command_info' => true,

        // Capture HTTP client request information as breadcrumbs
        'http_client_requests' => true,
    ],

    // Performance monitoring specific configuration
    'tracing' => [
        // Trace queue jobs as their own transactions (this enables tracing for queue jobs)
        'queue_job_transactions' => true,

        // Capture queue jobs as spans when executed on the sync driver
        'queue_jobs' => true,

        // Capture SQL queries as spans
        'sql_queries' => true,

        // Capture where the SQL query originated from on the SQL query spans
        'sql_origin' => true,

        // Capture views rendered as spans
        'views' => true,

        // Capture Livewire components as spans
        'livewire' => false,

        // Capture HTTP client requests as spans
        'http_client_requests' => true,

        // Capture Redis operations as spans (this enables Redis events in Laravel)
        'redis_commands' => true,

        // Capture where the Redis command originated from on the Redis command spans
        'redis_origin' => true,

        // Enable tracing for requests without a matching route (404's)
        'missing_routes' => false,

        // Configures if the performance trace should continue after the response has been sent to the user until the application terminates
        // This is required to capture any spans that are created after the response has been sent like queue jobs dispatched using `dispatch(...)->afterResponse()` for example
        'continue_after_response' => true,

        // Enable the tracing integrations supplied by Sentry (recommended)
        'default_integrations' => true,
    ],

];
