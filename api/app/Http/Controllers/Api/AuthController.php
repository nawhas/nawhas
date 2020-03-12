<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Transformers\UserTransformer;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private StatefulGuard $guard;

    public function __construct(AuthFactory $auth, UserTransformer $transformer)
    {
        $this->guard = $auth->guard('web');
        $this->transformer = $transformer;
    }

    public function login(Request $request): JsonResponse
    {
        if (!$this->guard->attempt($request->only(['email', 'password']))) {
            throw new AuthenticationException(__('auth.failed'));
        }

        return $this->respondWithItem($this->guard->user());
    }

    public function user()
    {
        return $this->guard->user();
    }
}
