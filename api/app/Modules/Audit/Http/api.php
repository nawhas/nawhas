<?php

use App\Modules\Audit\Http\Controllers;

Route::prefix('v1')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Revisions Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('revisions')->group(function () {
        Route::get('/', [Controllers\RevisionsController::class, 'index']);
    });
});
