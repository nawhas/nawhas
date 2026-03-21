<?php

declare(strict_types=1);

namespace App\Modules\Stories;

use App\Modules\Stories\Models\Story;
use App\Modules\Stories\Policies\StoryPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route as Router;
use Illuminate\Support\ServiceProvider as CoreServiceProvider;

class ServiceProvider extends CoreServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Story::class, StoryPolicy::class);

        $this->defineRoutes();
    }

    protected function defineRoutes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Router::middleware('api')
            ->namespace(__NAMESPACE__)
            ->group(__DIR__ . '/Http/api.php');
    }
}
