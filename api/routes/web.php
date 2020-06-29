<?php

use App\Http\Controllers;
use App\Modules\Features\Definitions as Features;
use App\Modules\Features\Http\Middleware\EnforceFeatureFlags;

Route::get('/alive', [Controllers\HealthCheckController::class, 'status']);

Route::get('/oauth/{provider}', [Controllers\OAuthController::class, 'redirect'])
    ->middleware(EnforceFeatureFlags::in([Features\PublicUserRegistration::NAME]));

Route::get('/oauth/{provider}/callback', [Controllers\OAuthController::class, 'callback'])
    ->middleware(EnforceFeatureFlags::in([Features\PublicUserRegistration::NAME]));
