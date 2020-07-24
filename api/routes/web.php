<?php

use App\Http\Controllers;
use App\Modules\Features\Definitions as Features;
use App\Modules\Features\Http\Middleware\EnforceFeatureFlags;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::get('/alive', [Controllers\HealthCheckController::class, 'status']);

// Backwards compatibility with Airlock
Route::get('/airlock/csrf-cookie', [CsrfCookieController::class, 'show']);
