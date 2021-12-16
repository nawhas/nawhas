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
    #[CoversEvent('user.registered')]
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
    #[CoversEvent('user.changed.password')]
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
        $this->assertTrue($hasher->check($newPassword, $user->password));
    }

    /**
     * @test
     */
    #[CoversEvent('user.changed.name')]
    public function it_can_replay_user_name_changed_event(): void
    {
        $user = $this->getUserFactory()->moderator();
        $name = $this->faker->name;

        $this->assertNotEquals($name, $user->name);

        $this->event('user.changed.name', [
            'id' => $user->id,
            'name' => $name,
        ]);

        $this->replay();

        $user->refresh();
        $this->assertEquals($name, $user->name);
    }

    /**
     * @test
     */
    #[CoversEvent('user.changed.email')]
    public function it_can_replay_user_email_changed_event(): void
    {
        $user = $this->getUserFactory()->moderator();
        $email = $this->faker->email;

        $this->assertNotEquals($email, $user->email);

        $this->event('user.changed.email', [
            'id' => $user->id,
            'email' => $email,
        ]);

        $this->replay();

        $user->refresh();
        $this->assertEquals($email, $user->email);
    }

    /**
     * @test
     */
    #[CoversEvent('user.changed.nickname')]
    public function it_can_replay_user_nickname_changed_event(): void
    {
        $user = $this->getUserFactory()->moderator();
        $nickname = $this->faker->userName;

        $this->assertNotEquals($nickname, $user->nickname);

        $this->event('user.changed.nickname', [
            'id' => $user->id,
            'nickname' => $nickname,
        ]);

        $this->replay();

        $user->refresh();
        $this->assertEquals($nickname, $user->nickname);
    }
}
