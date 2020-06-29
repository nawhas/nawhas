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
                "email" => "shea7862@live.co.uk"
            ],
            "entity" => "reciter",
            "entityId" => "a6655aa2-b9c4-11ea-864d-0242ac120005",
            "old" => null,
            "new" => [
                "name" => "Asif Ali",
                "description" => null,
                "avatar" => null,
                "createdAt" => null,
                "updatedAt" => null
            ]
        ]);
    }
}
