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
use App\Repositories\UserProviderRepository;
use Illuminate\Contracts\Auth\{Factory as AuthFactory, StatefulGuard};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    private StatefulGuard $guard;
    private EntityManager $em;
    private UserProviderRepository $userProviderRepository;

    public function __construct(AuthFactory $auth, UserTransformer $transformer, EntityManager $em, UserProviderRepository $userProviderRepository)
    {
        $this->guard = $auth->guard('web');
        $this->transformer = $transformer;
        $this->em = $em;
        $this->userProviderRepository = $userProviderRepository;
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

    public function getSocialRedirect($provider)
    {
        try {
            return Socialite::with($provider)->stateless()->redirect();
        } catch ( \InvalidArgumentException $e ){
            return $e;
        }
    }

    public function getSocialCallback($provider): JsonResponse
    {
        $socialUser = Socialite::with($provider)->stateless()->user();

        $userProvider = $this->userProviderRepository->findByProviderId($provider, $socialUser->getId());
        $user = $userProvider->getUser();
        $this->guard->login($user, false);

        return $this->respondWithItem($this->guard->user());
    }
}
