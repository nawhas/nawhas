<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class HealthCheckController extends Controller
{
    public function status(): JsonResponse
    {
        return response()->json([
            'status' => 'OK',
            'config' => [
                'database' => [
                    'data' => Arr::only(config('database.connections.data'), ['host', 'database']),
                    'events' => Arr::only(config('database.connections.events'), ['host', 'database']),
                ],
            ]
        ]);
    }
}
