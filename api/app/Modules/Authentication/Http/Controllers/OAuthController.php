<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Authentication\Enum\Role;
use App\Modules\Authentication\Models\SocialAccount;
use App\Modules\Authentication\Models\User;
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

    public function __construct(AuthFactory $auth, Socialite $socialite)
    {
        $this->guard = $auth->guard('web');
        $this->socialite = $socialite;
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

        // Need to change this to the new Repo
        $socialAccount = SocialAccount::findByProviderId($provider, $socialiteUser->getId())

        if ($socialAccount !== null) {
            $user = $socialAccount->user;
        } else {
            $user = $this->persistUser($provider, $socialiteUser);
        }

        $this->guard->login($user);

        return redirect()->to(config('app.url'));
    }

    private function persistUser(string $provider, SocialiteUser $socialiteUser): User
    {
        $user = User::findByEmail($socialiteUser->getEmail());

        if ($user !== null) {
            // If the user email already exists
            // then we need to redirect to a "connect account" screen.
            // For now, we'll just throw an exception.

            throw new \RuntimeException('Not ready.');
        }

        $user = User::create(
            Role::CONTRIBUTOR(),
            $socialiteUser->getName(),
            $socialiteUser->getEmail(),
        );

        SocialAccount::create($user->id, $provider, $socialiteUser->getId());

        return $user;
    }
}
