<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Transformers\UserTransformer;
use Illuminate\Auth\AuthenticationException;
use App\Entities\User;
use App\Enum\Role;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\{Factory as AuthFactory, StatefulGuard};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    private StatefulGuard $guard;
    private UserRepository $userRepository;

    public function __construct(AuthFactory $auth, UserTransformer $transformer, UserRepository $userRepository)
    {
        $this->guard = $auth->guard('web');
        $this->transformer = $transformer;
        $this->userRepository = $userRepository;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!$this->guard->attempt($request->credentials(), $request->shouldRemember())) {
            throw new AuthenticationException(__('auth.failed'));
        }

        return $this->respondWithItem($this->guard->user());
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $role = Role::CONTRIBUTOR();
        $name = $request->get('name');
        $email = $request->get('email');
        $password = $request->get('password');

        $user = new User($role, $name, $email);
        $user->setPassword($password);

        if ($request->get('nickname')) {
            $user->setNickname($request->get('nickname'));
        }

        $this->userRepository->persist($user);

        $this->guard->login($user, false);

        return $this->respondWithItem($this->guard->user());
    }

    public function logout(): Response
    {
        $this->guard->logout();

        return response()->noContent();
    }

    public function user(): JsonResponse
    {
        return $this->respondWithItem($this->guard->user());
    }
}
