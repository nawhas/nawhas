<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Projectors;

use App\Modules\Authentication\Events\UserEmailChanged;
use App\Modules\Authentication\Events\UserNameChanged;
use App\Modules\Authentication\Events\UserNicknameChanged;
use App\Modules\Authentication\Events\UserPasswordChanged;
use App\Modules\Authentication\Events\UserRegistered;
use App\Modules\Authentication\Events\UserRememberTokenChanged;
use App\Modules\Authentication\Events\UserRoleChanged;
use App\Modules\Authentication\Models\User;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UsersProjector extends Projector
{
    public function onUserRegistered(UserRegistered $event): void
    {
        $data = collect($event->attributes);
        $data->put('id', $event->id);

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

    public function onUserRoleChanged(UserRoleChanged $event): void
    {
        $user = User::retrieve($event->id);
        $user->role = $event->role;
        $user->saveOrFail();
    }

    public function onUserNicknameChanged(UserNicknameChanged $event): void
    {
        $user = User::retrieve($event->id);
        $user->nickname = $event->nickname;
        $user->saveOrFail();
    }

    public function onUserRememberTokenChanged(UserRememberTokenChanged $event): void
    {
        $user = User::retrieve($event->id);
        $user->remember_token = $event->rememberToken;
        $user->saveOrFail();
    }
}
