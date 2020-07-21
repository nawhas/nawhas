<?php

use App\Modules\Library\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('v2/library')->group(function() {
    Route::post('reciters', [Controllers\RecitersController::class, 'store']);
    Route::get('reciters/{id}', [Controllers\RecitersController::class, 'show']);
});
