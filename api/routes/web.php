<?php

use App\Http\Controllers;
use App\Modules\Features\Definitions as Features;
use App\Modules\Features\Http\Middleware\EnforceFeatureFlags;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::get('/alive', [Controllers\HealthCheckController::class, 'status']);

Route::get('/oauth/{provider}', [Controllers\OAuthController::class, 'redirect'])
    ->middleware(EnforceFeatureFlags::in([
        Features\SocialAuthentication::NAME,
    ]));

Route::get('/oauth/{provider}/callback', [Controllers\OAuthController::class, 'callback'])
    ->middleware(EnforceFeatureFlags::in([
        Features\SocialAuthentication::NAME,
    ]));

// Backwards compatibility with Airlock
Route::get('/airlock/csrf-cookie', [CsrfCookieController::class, 'show']);
