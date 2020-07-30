<?php

use App\Http\Controllers;
use App\Http\Controllers\VendorAssetsController;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::get('/alive', [Controllers\HealthCheckController::class, 'status']);

// Backwards compatibility with Airlock
Route::get('/airlock/csrf-cookie', [CsrfCookieController::class, 'show']);

Route::get('/vendor/{asset}', [VendorAssetsController::class, 'show'])->where('asset', '.*');
