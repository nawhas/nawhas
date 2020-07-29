<?php

use App\Modules\Authentication\Http\Controllers;
use App\Modules\Features\Definitions\PublicUserRegistration;
use App\Modules\Features\Http\Middleware\EnforceFeatureFlags;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        Route::post('login', [Controllers\AuthController::class, 'login']);
        Route::post('register', [Controllers\AuthController::class, 'register'])
            ->middleware(EnforceFeatureFlags::in([PublicUserRegistration::NAME]));
        Route::post('logout', [Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('user', [Controllers\AuthController::class, 'user'])->middleware('auth:sanctum');
    });
});
