<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\ResponseCache\Middlewares\CacheResponse as BaseCacheResponse;
use Symfony\Component\HttpFoundation\Response;
use TiMacDonald\Middleware\HasParameters;

class CacheResponse
{
    use HasParameters;

    private BaseCacheResponse $wrapped;

    public function __construct(BaseCacheResponse $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    public function handle(Request $request, Closure $next, string ...$tags): Response
    {
        return $this->wrapped->handle($request, $next, ...$tags);
    }

    public static function withTags(string ...$tags): string
    {
        return self::with([
            'tags' => $tags,
        ]);
    }
}
