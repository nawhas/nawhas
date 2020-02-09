<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class HealthCheckController extends Controller
{
    public function status(): JsonResponse
    {
        return response()->json(['status' => 'OK']);
    }
}
