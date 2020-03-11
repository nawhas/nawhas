<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private AuthFactory $auth;

    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    public function login(Request $request): Response
    {
        if (!$this->guard('web')->attempt($request->only(['email', 'password']))) {
            throw new AuthenticationException(__('auth.failed'));
        }

        return response()->noContent();
    }

    public function user()
    {
        return $this->guard('web')->user();
    }

    private function guard(?string $driver = null): StatefulGuard
    {
        return $this->auth->guard($driver);
    }
}
