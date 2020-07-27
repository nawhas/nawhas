<?php

use App\Modules\Library\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Reciter Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('reciters')->group(function () {
        Route::get('/', [Controllers\RecitersController::class, 'index']);
        Route::get('/{reciter}', [Controllers\RecitersController::class, 'show']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [Controllers\RecitersController::class, 'store']);
            Route::patch('/{reciter}', [Controllers\RecitersController::class, 'update']);
            Route::post('/{reciter}/avatar', [Controllers\RecitersController::class, 'uploadAvatar']);
            Route::delete('/{reciter}', [Controllers\RecitersController::class, 'delete']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Album Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('reciters/{reciter}/albums')->group(function () {
        Route::get('/', [Controllers\AlbumsController::class, 'index']);
        Route::get('/{album:year}', [Controllers\AlbumsController::class, 'show']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [Controllers\AlbumsController::class, 'store']);
            Route::patch('/{album:year}', [Controllers\AlbumsController::class, 'update']);
            Route::post('/{album:year}/artwork', [Controllers\AlbumsController::class, 'uploadArtwork']);
            Route::delete('/{album:year}', [Controllers\AlbumsController::class, 'destroy']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Track Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('reciters/{reciter}/albums/{album:year}/tracks')->group(function () {
        Route::get('/', [Controllers\TracksController::class, 'index']);
        Route::get('/{track:slug}', [Controllers\TracksController::class, 'show']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [Controllers\TracksController::class, 'store']);
            Route::patch('/{track:slug}', [Controllers\TracksController::class, 'update']);
            Route::post('/{track:slug}/media/audio', [Controllers\TracksController::class, 'uploadTrackMedia']);
            Route::delete('/{track:slug}', [Controllers\TracksController::class, 'destroy']);
        });
    });
});
