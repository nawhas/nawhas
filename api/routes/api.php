<?php

use App\Http\Controllers\Api\FeaturesController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\PopularEntitiesController;
use App\Http\Controllers\Api\SearchController;
use App\Modules\Features\Definitions\PublicUserRegistration;
use App\Modules\Features\Http\Middleware\EnforceFeatureFlags;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {
    // App
    Route::prefix('app')->group(function () {
        Route::post('feedback', [FeedbackController::class, 'submit']);
    });

    Route::get('search/key', [SearchController::class, 'key']);
});
