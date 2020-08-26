<?php

use App\Modules\Accounts\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Library Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('me')->middleware('auth:sanctum')->group(function () {
        Route::get('/tracks', [Controllers\LibraryController::class, 'tracks']);
        Route::put('/tracks', [Controllers\LibraryController::class, 'saveTracks']);
        Route::delete('/tracks', [Controllers\LibraryController::class, 'removeSavedTracks']);
    });
});
