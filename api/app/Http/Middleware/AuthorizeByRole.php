<?php

namespace App\Http\Middleware;

use App\Modules\Authentication\Enum\Role;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Validation\UnauthorizedException;

class AuthorizeByRole
{
    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(
        protected Auth $auth
    ) {}

    /**
     * Handle an incoming request.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, string $role)
    {
        if ($this->auth->guard()->guest()) {
            throw new AuthenticationException();
        }

        /** @var \App\Modules\Authentication\Models\User $user */
        $user = $this->auth->guard()->user();
        $role = Role::from($role);
        if ($user->role !== $role) {
            throw new UnauthorizedException('Insufficient role');
        }

        return $next($request);
    }
}
