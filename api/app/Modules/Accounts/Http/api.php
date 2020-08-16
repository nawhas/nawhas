<?php

use App\Modules\Accounts\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Library Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('me')->group(function () {
        Route::get('/tracks', [Controllers\LibraryController::class, 'tracks']);
        Route::put('/tracks', [Controllers\LibraryController::class, 'addTrack']);
        Route::delete('/tracks', [Controllers\LibraryController::class, 'removeTrack']);
    });
});
