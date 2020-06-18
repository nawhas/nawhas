<?php

declare(strict_types=1);

namespace App\Modules\Features\Http\Middleware;

use App\Modules\Features\Exceptions\FeatureNotEnabledException;
use App\Modules\Features\FeatureManager;
use Closure;
use Illuminate\Http\Request;
use TiMacDonald\Middleware\HasParameters;

class EnforceFeatureFlags
{
    use HasParameters;

    private FeatureManager $features;

    public function __construct(FeatureManager $features)
    {
        $this->features = $features;
    }

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$features)
    {
        foreach ($features as $feature) {
            if (!$this->features->enabled($feature)) {
                throw new FeatureNotEnabledException($feature);
            }
        }

        return $next($request);
    }
}
