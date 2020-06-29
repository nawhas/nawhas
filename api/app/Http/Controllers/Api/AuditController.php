<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class AuditController extends Controller
{
    public function index()
    {
        return response()->json([
            "id" => "a66d7066-b9c4-11ea-8153-0242ac120005",
            "type" => "created",
            "user" => [
                "name" => "Asif Ali",
                "role" => "moderator",
                "email" => "asif@nawhas.com"
            ],
            "entity" => "reciter",
            "entityId" => "a6655aa2-b9c4-11ea-864d-0242ac120005",
            // "old" => [
            //     "name" => "Asif Ali",
            //     "description" => null,
            //     "avatar" => null,
            //     "createdAt" => null,
            //     "updatedAt" => null
            // ],
            "old" => null,
            "new" => [
                "name" => "Zain Mehdi",
                "description" => 'Description has been changed',
                "avatar" => null,
                "createdAt" => null,
                "updatedAt" => null
            ]
        ]);
    }
}
