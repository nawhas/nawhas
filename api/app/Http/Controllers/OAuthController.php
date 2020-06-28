<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Entities\SocialAccount;
use App\Entities\User;
use App\Enum\Role;
use App\Repositories\SocialAccountRepository;
use App\Repositories\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use OAuthException;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class OAuthController extends Controller
{
    private StatefulGuard $guard;
    private Socialite $socialite;
    private SocialAccountRepository $socialAccountRepo;
    private UserRepository $userRepo;

    public function __construct(
        AuthFactory $auth,
        Socialite $socialite,
        SocialAccountRepository $repository,
        UserRepository $userRepo
    ) {
        $this->guard = $auth->guard('web');
        $this->socialite = $socialite;
        $this->socialAccountRepo = $repository;
        $this->userRepo = $userRepo;
    }


    public function redirect(string $provider): SymfonyRedirectResponse
    {
        abort_unless(in_array($provider, $this->getSupportedProviders()), 404);

        return $this->socialite->driver($provider)->redirect();
    }

    /**
     * @return array|string[]
     */
    private function getSupportedProviders(): array
    {
        return config('services.oauth.enabled', []);
    }

    /**
     * @throws AuthenticationException
     */
    public function callback(string $provider): RedirectResponse
    {
        $socialiteUser = $this->socialite->driver($provider)->user();
        $socialAccount = $this->socialAccountRepo->findByProviderId($provider, $socialiteUser->getId());

        if ($socialAccount !== null) {
            $user = $socialAccount->getUser();
        } else {
            $user = $this->persistUser($provider, $socialiteUser);
        }

        $this->guard->login($user);

        return redirect(config('app.url'));
    }

    private function persistUser(string $provider, SocialiteUser $socialiteUser): User
    {
        $user = $this->userRepo->findByEmail($socialiteUser->getEmail());

        if ($user !== null) {
            // If the user email already exists
            // then we need to redirect to a "connect account" screen.
            // For now, we'll just throw an exception.

            throw new OAuthException(__('auth.exists'));
        }

        $user = new User(
            Role::CONTRIBUTOR(),
            $socialiteUser->getName(),
            $socialiteUser->getEmail(),
        );

        $this->socialAccountRepo->persist(
            new SocialAccount($user, $provider, $socialiteUser->getId())
        );

        return $user;
    }
}
