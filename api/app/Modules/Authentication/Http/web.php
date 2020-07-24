<?php

use App\Modules\Authentication\Http\Controllers;
use App\Modules\Features\Definitions as Features;
use App\Modules\Features\Http\Middleware\EnforceFeatureFlags;
use Illuminate\Support\Facades\Route;

Route::get('/oauth/{provider}', [Controllers\OAuthController::class, 'redirect'])
    ->middleware(EnforceFeatureFlags::in([
        Features\SocialAuthentication::NAME,
    ]));

Route::get('/oauth/{provider}/callback', [Controllers\OAuthController::class, 'callback'])
    ->middleware(EnforceFeatureFlags::in([
        Features\SocialAuthentication::NAME,
    ]));
