<?php

use App\Http\Controllers\Api\AlbumsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FeaturesController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\PopularEntitiesController;
use App\Http\Controllers\Api\RecitersController;
use App\Http\Controllers\Api\TracksController;
use App\Modules\Features\Definitions\PublicUserRegistration;
use App\Modules\Features\Http\Middleware\EnforceFeatureFlags;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    // App
    Route::prefix('app')->group(function () {
        Route::post('feedback', [FeedbackController::class, 'submit']);
    });

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register'])
            ->middleware(EnforceFeatureFlags::in([PublicUserRegistration::NAME]));
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('user', [AuthController::class, 'user'])->middleware('auth:sanctum');
    });

    // Features
    Route::prefix('features')->group(function () {
        Route::get('/', [FeaturesController::class, 'index']);
        Route::get('secret', [FeaturesController::class, 'secret'])
            ->middleware(EnforceFeatureFlags::in([PublicUserRegistration::NAME]));
    });

    // Popular Routes
    Route::prefix('popular')->group(function () {
        Route::get('/reciters', [PopularEntitiesController::class, 'reciters']);
        Route::get('/tracks', [PopularEntitiesController::class, 'tracks']);
    });
});
