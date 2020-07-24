<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Authentication\Enum\Role;
use App\Modules\Authentication\Events\UserLoggedIn;
use App\Modules\Authentication\Http\Requests\LoginRequest;
use App\Modules\Authentication\Http\Requests\RegisterRequest;
use App\Modules\Authentication\Http\Transformers\UserTransformer;
use Illuminate\Contracts\Auth\{Factory as AuthFactory, StatefulGuard};
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(AuthFactory $auth, UserTransformer $transformer)
    {
        $this->guard = $auth->guard('web');
        $this->transformer = $transformer;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!$this->guard->attempt($request->credentials(), $request->shouldRemember())) {
            throw new AuthenticationException(__('auth.failed'));
        }

        event(new UserLoggedIn($this->guard->id()));

        return $this->respondWithItem($this->guard->user());
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $role = Role::CONTRIBUTOR();
    }
}
