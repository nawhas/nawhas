<?php

namespace App\Modules\Authentication\Http\Middleware;

use App\Modules\Authentication\Models\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TiMacDonald\Middleware\HasParameters;

class EnforceRole
{
    use HasParameters;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $role
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle($request, Closure $next, string $role)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== $role) {
            throw new AuthorizationException();
        }

        return $next($request);
    }
}
