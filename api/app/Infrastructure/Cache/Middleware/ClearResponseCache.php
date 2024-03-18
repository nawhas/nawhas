<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\ResponseCache\ResponseCache;
use Symfony\Component\HttpFoundation\Response;
use TiMacDonald\Middleware\HasParameters;

class ClearResponseCache
{
    use HasParameters;

    public function __construct(
        protected ResponseCache $manager
    ) {}

    public static function withTags(string ...$tags): string
    {
        return self::with(['tags' => $tags]);
    }

    public function handle(Request $request, Closure $next, string ...$tags): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if ($response->isSuccessful()) {
            $this->manager->clear($tags);
        }

        return $response;
    }
}
