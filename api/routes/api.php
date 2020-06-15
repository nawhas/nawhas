<?php

use App\Http\Controllers\Api\AlbumsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FeaturesController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\PopularEntitiesController;
use App\Http\Controllers\Api\RecitersController;
use App\Http\Controllers\Api\TracksController;
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
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:airlock');
        Route::get('user', [AuthController::class, 'user'])->middleware('auth:airlock');
    });

    // Features
    Route::prefix('features')->group(function () {
        Route::get('/', [FeaturesController::class, 'index']);
    });

    // Reciters
    Route::prefix('reciters')->group(function () {
        Route::get('/', [RecitersController::class, 'index']);
        Route::get('/{reciter}', [RecitersController::class, 'show']);

        Route::middleware('auth:airlock')->group(function () {
            Route::post('/', [RecitersController::class, 'store']);
            Route::patch('/{reciter}', [RecitersController::class, 'update']);
            Route::post('/{reciter}/avatar', [RecitersController::class, 'uploadAvatar']);
            // Route::delete('/{reciter}', [RecitersController::class, 'destroy']);
        });
    });

    // Reciter Albums
    Route::prefix('reciters/{reciter}/albums')->group(function () {
        Route::get('/', [AlbumsController::class, 'index']);
        Route::get('/{album}', [AlbumsController::class, 'show']);

        Route::middleware('auth:airlock')->group(function () {
            Route::post('/', [AlbumsController::class, 'store']);
            Route::patch('/{album}', [AlbumsController::class, 'update']);
            Route::post('/{album}/artwork', [AlbumsController::class, 'uploadArtwork']);
            Route::delete('/{album}', [AlbumsController::class, 'destroy']);
        });
    });

    // Album Tracks
    Route::prefix('reciters/{reciter}/albums/{album}/tracks')->group(function () {
        Route::get('/', [TracksController::class, 'index']);
        Route::get('/{track}', [TracksController::class, 'show']);

        Route::middleware('auth:airlock')->group(function () {
            Route::post('/', [TracksController::class, 'store']);
            Route::patch('/{track}', [TracksController::class, 'update']);
            Route::post('/{track}/media/audio', [TracksController::class, 'uploadTrackMedia']);
            Route::delete('/{track}', [TracksController::class, 'destroy']);
        });
    });

    // Popular Routes
    Route::prefix('popular')->group(function () {
        Route::get('/reciters', [PopularEntitiesController::class, 'reciters']);
        Route::get('/tracks', [PopularEntitiesController::class, 'tracks']);
    });
});
