<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Illuminate\Support\Collection;
use Tests\Feature\FeatureTestCase;
use Tests\WithSimpleFaker;

class RegisterTest extends FeatureTestCase
{
    use WithSimpleFaker;

    private const ROUTE_REGISTER = 'v1/auth/register';

    /**
     * @test
     */
    public function it_allows_registering_a_new_user(): void
    {
        $request = [
            'name' => static::faker()->name,
            'email' => static::faker()->email,
            'password' => static::faker()->password,
        ];

        $this->postJson(self::ROUTE_REGISTER, $request)
            ->assertOk()
            ->assertJsonStructure([
                'id',
                'name',
                'nickname',
                'avatar',
                'email',
                'role',
                'createdAt',
                'updatedAt',
            ])
            ->assertJsonPath('name', $request['name'])
            ->assertJsonPath('email', $request['email'])
            ->assertJsonMissing(['password' => $request['password']]);

        $this->assertAuthenticated();
    }

    /**
     * @test
     */
    public function it_ensures_unique_email(): void
    {
        $email = 'someone@nawhas.com';
        $this->getUserFactory()->contributor(['email' => $email]);

        $request = [
            'name' => static::faker()->name,
            'email' => $email,
            'password' => static::faker()->password,
        ];

        $this->postJson(self::ROUTE_REGISTER, $request)
            ->assertJsonValidationErrors(['email' => 'taken']);

        $this->assertGuest();
    }

    /**
     * @test
     * @dataProvider provideInvalidRequests
     */
    public function it_validates_fields(Collection $request, array $errors): void
    {
        $this->postJson(self::ROUTE_REGISTER, $request->all())
            ->assertJsonValidationErrors($errors);

        $this->assertGuest();
    }

    public static function provideInvalidRequests(): array
    {
        $defaults = collect([
            'name' => self::faker()->name,
            'email' => self::faker()->email,
            'password' => self::faker()->password,
        ]);

        return [
            'email' => [
                'request' => $defaults->merge(['email' => 'not-an-email']),
                'errors' => ['email']
            ],
            'password' => [
                'request' => $defaults->merge(['password' => '']),
                'errors' => ['password']
            ],
            'name' => [
                'request' => $defaults->merge(['name' => '']),
                'errors' => ['name']
            ],
        ];
    }
}
