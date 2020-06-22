<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Database\Doctrine\EntityManager;
use App\Entities\SocialAccount;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Transformers\UserTransformer;
use Illuminate\Auth\AuthenticationException;
use App\Entities\User;
use App\Enum\Role;
use App\Repositories\SocialAccountRepository;
use App\Repositories\UserProviderRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\{Factory as AuthFactory, StatefulGuard};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    private StatefulGuard $guard;
    private UserRepository $userRepository;
    private SocialAccountRepository $socialAccountRepository;

    public function __construct(AuthFactory $auth, UserTransformer $transformer, UserRepository $userRepository, SocialAccountRepository $socialAccountRepository)
    {
        $this->guard = $auth->guard('web');
        $this->transformer = $transformer;
        $this->userRepository = $userRepository;
        $this->socialAccountRepository = $socialAccountRepository;
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
        $socialUserId = $socialUser->getId();

        $SocialAccount = $this->socialAccountRepository->findByProviderId($provider, $socialUserId);

        if ($SocialAccount) {
            $user = $SocialAccount->getUser();
        } else {
            $user = $this->userRepository->findByEmail($socialUser->getEmail());
            if (!$user) {
                $role = Role::CONTRIBUTOR();
                $name = $socialUser->getName();
                $email = $socialUser->getEmail();

                $user = new User($role, $name, $email);
                $this->userRepository->persist($user);
            }
            $SocialAccount = new SocialAccount($user, $provider, $socialUserId);
            $this->socialAccountRepository->persist($SocialAccount);
        }
        $this->guard->login($user, false);

        return $this->respondWithItem($this->guard->user());
    }
}
