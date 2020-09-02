<?php

use App\Infrastructure\Cache\Middleware\CacheResponse;
use App\Modules\Features\Http\CacheTags;
use App\Modules\Features\Http\Controllers\FeaturesController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Features
    Route::get('/features', [FeaturesController::class, 'index'])
        ->middleware(CacheResponse::withTags(CacheTags::FEATURE_LIST));
});
