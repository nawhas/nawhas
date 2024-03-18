<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Projectors;

use Illuminate\Support\Str;
use App\Modules\Authentication\Events\{
    UserEmailChanged,
    UserNameChanged,
    UserNicknameChanged,
    UserPasswordChanged,
    UserRegistered,
};
use App\Modules\Authentication\Models\User;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UsersProjector extends Projector
{
    public function onUserRegistered(UserRegistered $event): void
    {
        $data = collect($event->attributes);
        $data->put('id', $event->id);

        // Convert camelCase keys to snake_case
        $data = $data->mapWithKeys(fn ($value, $key) => [Str::snake($key) => $value]);

        $user = new User($data->all());

        $user->saveOrFail();
    }

    public function onUserNameChanged(UserNameChanged $event): void
    {
        $user = User::retrieve($event->id);
        $user->name = $event->name;
        $user->saveOrFail();
    }

    public function onUserEmailChanged(UserEmailChanged $event): void
    {
        $user = User::retrieve($event->id);
        $user->email = $event->email;
        $user->saveOrFail();
    }

    public function onUserPasswordChanged(UserPasswordChanged $event): void
    {
        $user = User::retrieve($event->id);
        $user->password = $event->password;
        $user->saveOrFail();
    }

    public function onUserNicknameChanged(UserNicknameChanged $event): void
    {
        $user = User::retrieve($event->id);
        $user->nickname = $event->nickname;
        $user->saveOrFail();
    }

    public function resetState(): void
    {
        User::truncate();
    }
}
