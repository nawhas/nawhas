<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Models;

use App\Entities\Contracts\TimestampedEntity;
use App\Modules\Authentication\Enum\Role;
use App\Modules\Authentication\Events\UserEmailChanged;
use App\Modules\Authentication\Events\UserNameChanged;
use App\Modules\Authentication\Events\UserNicknameChanged;
use App\Modules\Authentication\Events\UserPasswordChanged;
use App\Modules\Authentication\Events\UserRegistered;
use App\Modules\Authentication\Events\UserRememberTokenChanged;
use App\Modules\Authentication\Events\UserRoleChanged;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ramsey\Uuid\Uuid;

class User extends Model implements TimestampedEntity
{
    protected $keyType = 'string';

    public static function create(Role $role, string $name, string $email, ?string $password = null, ?bool $rememberToken = null, ?string $nickname = null ): self
    {
        $id = Uuid::uuid1()->toString();

        event(new UserRegistered($id, [
            'role' => $role,
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'rememberToken' => $rememberToken,
            'nickname' => $nickname,
        ]));

        return self::retrieve($id);
    }

    /**
     * @param string $identifier
     * @throws ModelNotFoundException
     * @return User
     */
    public static function retrieve(string $identifier): self
    {
        /** @var self $model */
        $model = self::query()->findOrFail($identifier);
        return $model;
    }

    public function changeName(string $name): void
    {
        if ($name !== $this->name) {
            event(new UserNameChanged($this->id, $name));
        }
    }

    public function changeRole(Role $role): void
    {
        if ($role !== $this->role) {
            event(new UserRoleChanged($this->id, $role));
        }
    }

    public function changeEmail(string $email): void
    {
        if ($email !== $this->email) {
            event(new UserEmailChanged($this->id, $email));
        }
    }

    public function changePassword(string $password):void
    {
        if ($password !== $this->password) {
            event(new UserPasswordChanged($this->id, $password));
        }
    }

    public function changeRememberToken(bool $rememberToken): void
    {
        if ($rememberToken !== $this->rememberToken) {
            event(new UserRememberTokenChanged($this->id, $rememberToken));
        }
    }

    public function changeNickname(?string $nickname):void
    {
        if ($nickname !== $this->nickname) {
            event(new UserNicknameChanged($this->id, $nickname));
        }
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return Carbon::make($this->created_at);
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return Carbon::make($this->updated_at);
    }
}
