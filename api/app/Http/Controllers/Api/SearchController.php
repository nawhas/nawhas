<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use MeiliSearch\Client as Search;
use MeiliSearch\Endpoints\Keys;

class SearchController extends Controller
{
    const SEARCH_API_KEY_DESCRIPTION = 'Default Search API Key'; // TODO - provision own keys

    public function __construct(
        private Search $search
    ) {}

    public function key(): JsonResponse
    {
        $keys = $this->search->getKeys();

        /** @var Keys $key */
        $key = collect($keys)
            ->first(fn (Keys $key) => str_starts_with($key->getDescription(), self::SEARCH_API_KEY_DESCRIPTION));

        return $this->respondWithArray([
            'data' => $key->getKey(),
        ]);
    }
}
