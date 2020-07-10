<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class AuditController extends Controller
{
    public function index()
    {
        return response()->json([
            "data" => [
                [
                    "id" => "a66d7066-b9c4-11ea-8153-0242ac120005",
                    "type" => "deleted",
                    "user" => [
                        "name" => "Asif Ali",
                        "role" => "moderator",
                        "email" => "asif@nawhas.com"
                    ],
                    "entity" => "album",
                    "entityId" => "a6655aa2-b9c4-11ea-864d-0242ac120005",
                    "old" => [
                        "name" => "Zain Mehdi",
                        "description" => null,
                        "avatar" => null,
                        "createdAt" => null,
                        "updatedAt" => null
                    ],
                    "new" => null
                ],
                [
                    "id" => "a66d7066-b9c4-3322-8153-0242acddd005",
                    "type" => "created",
                    "user" => [
                        "name" => "Asif Ali",
                        "role" => "moderator",
                        "email" => "asif@nawhas.com"
                    ],
                    "entity" => "reciter",
                    "entityId" => "a6655aa2-b9c4-11ea-864d-0242ac120005",
                    "old" => null,
                    "new" => [
                        "name" => "Zain Mehdi",
                        "description" => 'Description has been changed',
                        "avatar" => null,
                        "createdAt" => null,
                        "updatedAt" => null
                    ]
                ],
                [
                    "id" => "a66d7066-b9c4-11ea-3211-0242acddd005",
                    "type" => "modified",
                    "user" => [
                        "name" => "Asif Ali",
                        "role" => "moderator",
                        "email" => "asif@nawhas.com"
                    ],
                    "entity" => "track",
                    "entityId" => "a6655aa2-b9c4-11ea-864d-0242ac120005",
                    "old" => [
                        "name" => "Zain Mehdi",
                        "description" => 'Description has been modified',
                        "avatar" => null,
                        "createdAt" => null,
                        "updatedAt" => null
                    ],
                    "new" => [
                        "name" => "Zain Mehdi",
                        "description" => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).',
                        "avatar" => null,
                        "createdAt" => null,
                        "updatedAt" => null
                    ]
                ],
                [
                    "id" => "a66d7326-b9c4-11ea-3211-0242acddd005",
                    "type" => "modified",
                    "user" => [
                        "name" => "Asif Ali",
                        "role" => "moderator",
                        "email" => "asif@nawhas.com"
                    ],
                    "entity" => "track",
                    "entityId" => "a6655aa2-b9c4-11ea-864d-0242ac120005",
                    "old" => [
                        "name" => "Asif Ali",
                        "description" => 'Description has been changed',
                        "avatar" => null,
                        "createdAt" => null,
                        "updatedAt" => null
                    ],
                    "new" => [
                        "name" => "Zain Mehdi",
                        "description" => 'Description has been modified',
                        "avatar" => 'http://avatar.com',
                        "createdAt" => null,
                        "updatedAt" => null
                    ]
                ]
            ]
        ]);
    }
}
