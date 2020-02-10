<?php

use App\Http\Controllers\Api\AlbumsController;
use App\Http\Controllers\Api\RecitersController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function() {
    // Reciters
    Route::prefix('reciters')->group(function () {
        Route::get('/', [RecitersController::class, 'index']);
        // Route::post('/', [RecitersController::class, 'store']);
        Route::get('/{reciter}', [RecitersController::class, 'show']);
        // Route::post('/{reciter}', [RecitersController::class, 'update']);
        // Route::patch('/{reciter}', [RecitersController::class, 'update']);
        // Route::delete('/{reciter}', [RecitersController::class, 'destroy']);
    });

    // Reciter Albums
    Route::prefix('reciters/{reciter}/albums')->group(function () {
        Route::get('/', [AlbumsController::class, 'index']);
        // Route::post('/', [RecitersController::class, 'store']);
        Route::get('/{reciter}', [RecitersController::class, 'show']);
        // Route::post('/{reciter}', [RecitersController::class, 'update']);
        // Route::patch('/{reciter}', [RecitersController::class, 'update']);
        // Route::delete('/{reciter}', [RecitersController::class, 'destroy']);
    });
});
