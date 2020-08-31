<?php

use App\Infrastructure\Cache\Middleware\CacheResponse;
use App\Infrastructure\Cache\Middleware\ClearResponseCache;
use App\Modules\Accounts\Http\CacheTags;
use App\Modules\Accounts\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Library Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('me')->middleware('auth:sanctum')->group(function () {
        Route::get('/tracks', [Controllers\LibraryController::class, 'tracks'])
            ->middleware(CacheResponse::withTags(CacheTags::SAVED_TRACKS));

        Route::get('/tracks/ids', [Controllers\LibraryController::class, 'getTrackIds'])
            ->middleware(CacheResponse::withTags(CacheTags::SAVED_TRACKS));;

        Route::put('/tracks', [Controllers\LibraryController::class, 'saveTracks'])
            ->middleware(ClearResponseCache::withTags(CacheTags::SAVED_TRACKS));

        Route::delete('/tracks', [Controllers\LibraryController::class, 'removeSavedTracks']);
    });
});
