<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use MeiliSearch\Client as Search;

class SearchController extends Controller
{
    public function __construct(
        private Search $search
    ) {}

    public function key(): JsonResponse
    {
        $keys = $this->search->getKeys();

        return $this->respondWithArray([
            'data' => $keys['public'],
        ]);
    }
}
