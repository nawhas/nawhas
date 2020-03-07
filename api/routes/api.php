<?php

use App\Http\Controllers\Api\AlbumsController;
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

Route::prefix('v1')->group(function() {
    // Reciters
    Route::prefix('reciters')->group(function () {
        Route::get('/', [RecitersController::class, 'index']);
        Route::post('/', [RecitersController::class, 'store']);
        Route::get('/{reciter}', [RecitersController::class, 'show']);
        Route::patch('/{reciter}', [RecitersController::class, 'update']);
        Route::post('/{reciter}/avatar', [RecitersController::class, 'uploadAvatar']);
        // Route::delete('/{reciter}', [RecitersController::class, 'destroy']);
    });

    // Reciter Albums
    Route::prefix('reciters/{reciter}/albums')->group(function () {
        Route::get('/', [AlbumsController::class, 'index']);
        Route::post('/', [AlbumsController::class, 'store']);
        Route::get('/{album}', [AlbumsController::class, 'show']);
        Route::patch('/{album}', [AlbumsController::class, 'update']);
        Route::post('/{album}/artwork', [AlbumsController::class, 'uploadArtwork']);
        // Route::delete('/{album}', [AlbumsController::class, 'destroy']);
    });

    // Album Tracks
    Route::prefix('reciters/{reciter}/albums/{album}/tracks')->group(function () {
        Route::get('/', [TracksController::class, 'index']);
        Route::post('/', [TracksController::class, 'store']);
        Route::get('/{track}', [TracksController::class, 'show']);
        Route::patch('/{track}', [TracksController::class, 'update']);
        Route::post('/{track}/media/audio', [TracksController::class, 'uploadTrackMedia']);
        Route::delete('/{track}', [TracksController::class, 'destroy']);
    });

    // Popular Routes
    Route::prefix('popular')->group(function () {
        Route::get('/reciters', [PopularEntitiesController::class, 'reciters']);
        Route::get('/tracks', [PopularEntitiesController::class, 'tracks']);
    });
});
