<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Database\Doctrine\EntityManager;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Transformers\UserTransformer;
use Illuminate\Auth\AuthenticationException;
use App\Entities\User;
use App\Enum\Role;
use Illuminate\Contracts\Auth\{Factory as AuthFactory, StatefulGuard};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    private StatefulGuard $guard;
    private EntityManager $em;

    public function __construct(AuthFactory $auth, UserTransformer $transformer, EntityManager $em)
    {
        $this->guard = $auth->guard('web');
        $this->transformer = $transformer;
        $this->em = $em;
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
        $password = bcrypt($request->get('password'));

        $user = new User($role, $name, $email, $password);

        if ($request->get('nickname')) {
            $user->setNickname($request->get('nickname'));
        }

        $this->em->persist($user);

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

    public function getSocialRedirect($social)
    {
        try {
            return Socialite::with($social)->stateless()->redirect();
        } catch ( \InvalidArgumentException $e ){
            return redirect('/login');
        }
    }

    public function getSocialCallback($social): void
    {
        $socialUser = Socialite::with($social)->user();
    }
}
