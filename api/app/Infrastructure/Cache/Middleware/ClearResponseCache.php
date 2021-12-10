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

    public function __construct(
        protected ResponseCache $manager
    ) {}

    public static function withTags(string ...$tags): string
    {
        return self::with(['tags' => $tags]);
    }

    /**
     * TODO - Unsure if mixed return type is accurate
     */
    public function handle(Request $request, Closure $next, string ...$tags): mixed
    {
        /** @var mixed|Response $response */
        $response = $next($request);

        if ($response->isSuccessful()) {
            $this->manager->clear($tags);
        }

        return $response;
    }
}
