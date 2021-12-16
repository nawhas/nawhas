<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Modules\Authentication\Models\User;
use Illuminate\Contracts\Hashing\Hasher;

use function App\Support\uuid;

class UserEventsTest extends EventsTest
{
    /**
     * @test
     */
    public function it_can_replay_user_registered_event(): void
    {
        $properties = [
            'id' => uuid(),
            'attributes' => [
                'name' => $this->faker->name,
                'role' => 'moderator',
                'email' => $this->faker->email,
                'password' => bcrypt($this->faker->password),
                'remember_token' => $this->faker->randomAscii,
                'nickname' => $this->faker->userName,
            ],
        ];

        $this->event('user.registered', $properties);

        $this->replay();

        $user = User::find($properties['id']);
        $this->assertNotNull($user);

        $attributes = $properties['attributes'];
        $this->assertEquals($attributes['name'], $user->name);
        $this->assertEquals($attributes['role'], $user->role);
        $this->assertEquals($attributes['email'], $user->email);
        $this->assertEquals($attributes['password'], $user->password);
        $this->assertEquals($attributes['remember_token'], $user->remember_token);
        $this->assertEquals($attributes['nickname'], $user->nickname);
    }

    /**
     * @test
     */
    public function it_can_replay_user_password_changed_event(): void
    {
        $hasher = app(Hasher::class);

        $user = $this->getUserFactory()->moderator();
        $newPassword = $this->faker->password;

        $this->assertFalse($hasher->check($newPassword, $user->password));

        $this->event('user.changed.password', [
            'id' => $user->id,
            'password' => bcrypt($newPassword),
        ]);

        $this->replay();

        $user->refresh();
        $this->assertNotNull($user);

        $this->assertTrue($hasher->check($newPassword, $user->password));
    }
}
