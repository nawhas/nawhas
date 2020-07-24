<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Authentication\Enum\Role;
use App\Modules\Authentication\Events\UserLoggedIn;
use App\Modules\Authentication\Events\UserLoggedOut;
use App\Modules\Authentication\Events\UserRegistered;
use App\Modules\Authentication\Http\Requests\LoginRequest;
use App\Modules\Authentication\Http\Requests\RegisterRequest;
use App\Modules\Authentication\Http\Transformers\UserTransformer;
use App\Modules\Authentication\Models\User;
use Illuminate\Contracts\Auth\{Factory as AuthFactory, StatefulGuard};
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    private StatefulGuard $guard;

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
        $name = $request->get('name');
        $email = $request->get('email');
        $password = $request->get('password');

        $nickname = null;
        if ($request->get('nickname')) {
            $nickname = $request->get('nickname');
        }

        $user = User::create($role, $name, $email, $password, null, $nickname);

        $this->guard->login($user, false);

        event(new UserLoggedIn($this->guard->id()));

        return $this->respondWithItem($this->guard->user());
    }

    public function logout(): Response
    {
        $id = $this->guard->id();
        $this->guard->logout();

        event(new UserLoggedOut($id));

        return response()->noContent();
    }

    public function user(): JsonResponse
    {
        return $this->respondWithItem($this->guard->user());
    }
}
