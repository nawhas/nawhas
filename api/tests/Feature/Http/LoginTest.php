<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Tests\Feature\FeatureTestCase;
use Tests\Feature\Http\Responses\UserResponse;

class LoginTest extends FeatureTestCase
{
    private const ROUTE_LOGIN = 'v1/auth/login';

    /**
     * @test
     */
    public function it_allows_logging_in(): void
    {
        $credentials = [
            'email' => 'someone@nawhas.com',
            'password' => 'secret'
        ];

        $user = $this->getUserFactory()->contributor($credentials);

        UserResponse::from($this->postJson(self::ROUTE_LOGIN, $credentials))
            ->assertSuccessful()
            ->assertUserIdMatches($user->id)
            ->assertNameMatches($user->name)
            ->getTestResponse();

        $this->assertAuthenticatedAs($user);
    }

    /**
     * @test
     */
    public function it_responds_with_unauthorized_when_incorrect_password_provided(): void
    {
        $credentials = [
            'email' => 'someone@nawhas.com',
            'password' => 'secret'
        ];

        $this->getUserFactory()->contributor($credentials);

        $credentials['password'] = 'wrong';

        $this->postJson(self::ROUTE_LOGIN, $credentials)
            ->assertUnauthorized();

        $this->assertGuest();
    }
}
