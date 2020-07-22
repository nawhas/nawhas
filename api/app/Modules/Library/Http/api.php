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
        Route::get('/{id}', [Controllers\RecitersController::class, 'show']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [Controllers\RecitersController::class, 'store']);
            Route::patch('/{id}', [Controllers\RecitersController::class, 'update']);
            Route::post('/{id}/avatar', [Controllers\RecitersController::class, 'uploadAvatar']);
            Route::delete('/{id}', [Controllers\RecitersController::class, 'delete']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Album Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('reciters/{reciterId}/albums')->group(function () {
        Route::get('/', [Controllers\AlbumsController::class, 'index']);
        Route::get('/{albumId}', [Controllers\AlbumsController::class, 'show']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [Controllers\AlbumsController::class, 'store']);
            Route::patch('/{albumId}', [Controllers\AlbumsController::class, 'update']);
            Route::post('/{albumId}/artwork', [Controllers\AlbumsController::class, 'uploadArtwork']);
            Route::delete('/{albumId}', [Controllers\AlbumsController::class, 'destroy']);
        });
    });
});
