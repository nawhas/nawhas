<?php

declare(strict_types=1);

use App\Infrastructure\Cache\Middleware\CacheResponse;
use App\Infrastructure\Cache\Middleware\ClearResponseCache;
use App\Modules\Stories\Http\CacheTags;
use App\Modules\Stories\Http\Controllers\StoriesController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(CacheResponse::withTags(CacheTags::STORIES))->group(function () {
    Route::prefix('stories')->group(function () {
        Route::get('/', [StoriesController::class, 'index']);
        Route::get('/{story}', [StoriesController::class, 'show']);

        Route::middleware([
            'auth:sanctum',
            ClearResponseCache::withTags(CacheTags::STORIES),
        ])->group(function () {
            Route::post('/', [StoriesController::class, 'store']);
            Route::patch('/{story}', [StoriesController::class, 'update']);
            Route::delete('/{story}', [StoriesController::class, 'destroy']);
        });
    });
});
