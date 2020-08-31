<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\ResponseCache\ResponseCache;
use TiMacDonald\Middleware\HasParameters;

class ClearResponseCache
{
    use HasParameters;

    protected ResponseCache $manager;

    public function __construct(ResponseCache $responseCache)
    {
        $this->manager = $responseCache;
    }

    public function handle(Request $request, Closure $next, string ...$tags)
    {
        /** @var Response $response */
        $response = $next($request);

        if ($response->isSuccessful()) {
            $this->manager->clear($tags);
        }

        return $response;
    }

    public static function withTags(string ...$tags): string
    {
        return self::with(['tags' => $tags]);
    }
}
