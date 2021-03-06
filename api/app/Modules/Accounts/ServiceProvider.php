<?php

declare(strict_types=1);

namespace App\Modules\Accounts;

use Illuminate\Support\Facades\Route as Router;
use Illuminate\Support\ServiceProvider as CoreServiceProvider;

class ServiceProvider extends CoreServiceProvider
{
    public function boot(): void
    {
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
