<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Illuminate\Support\Collection;
use Tests\Feature\FeatureTest;
use Tests\WithSearchIndex;
use Tests\WithSimpleFaker;

class RegisterTest extends FeatureTest
{
    use WithSimpleFaker;
    use WithSearchIndex;

    private const ROUTE_REGISTER = 'v1/auth/register';

    /**
     * @test
     */
    public function it_allows_registering_a_new_user(): void
    {
        $request = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password,
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
            'name' => $this->faker->name,
            'email' => $email,
            'password' => $this->faker->password,
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

    public function provideInvalidRequests(): array
    {
        $this->setUpFaker();

        $defaults = collect([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password,
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
