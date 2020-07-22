<?php

use App\Modules\Library\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function() {
    Route::get('reciters', [Controllers\RecitersController::class, 'index']);
    Route::post('reciters', [Controllers\RecitersController::class, 'store']);
    Route::get('reciters/{id}', [Controllers\RecitersController::class, 'show']);
    Route::patch('reciters/{id}', [Controllers\RecitersController::class, 'update']);
    Route::post('reciters/{id}/avatar', [Controllers\RecitersController::class, 'uploadAvatar']);
    Route::delete('reciters/{id}', [Controllers\RecitersController::class, 'delete']);
});
