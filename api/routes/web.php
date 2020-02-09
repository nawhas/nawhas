<?php

use App\Http\Controllers;

Route::get('/alive', [Controllers\HealthCheckController::class, 'status']);
