<?php

use App\Modules\Popular\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Popular Routes
    Route::prefix('popular')->group(function () {
        Route::get('reciters', [Controllers\PopularEntitiesController::class, 'reciters']);
        Route::get('tracks', [Controllers\PopularEntitiesController::class, 'tracks']);
    });
});
