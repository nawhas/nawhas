<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Models;

use App\Modules\Authentication\Enum\Role;
use App\Modules\Authentication\Events\{UserEmailChanged,
    UserNameChanged,
    UserNicknameChanged,
    UserPasswordChanged,
    UserRegistered,
    UserRememberTokenChanged,
    UserRoleChanged};
use App\Modules\Core\Contracts\TimestampedEntity;
use App\Modules\Core\Models\{HasTimestamps, HasUuid, UsesDataConnection};
use App\Modules\Library\Models\Track;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Ramsey\Uuid\Uuid;

/**
 * App\Modules\Authentication\Models\User
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $nickname
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Track[] $savedTracks
 * @property-read int|null $saved_tracks_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements TimestampedEntity
{
    use HasUuid;
    use HasTimestamps;
    use UsesDataConnection;

    protected $guarded = [];

    public static function create(
        Role $role,
        string $name,
        string $email,
        ?string $password = null,
        ?bool $rememberToken = null,
        ?string $nickname = null
    ): static {
        $id = Uuid::uuid1()->toString();

        event(new UserRegistered($id, [
            'role' => $role->value,
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'remember_token' => $rememberToken,
            'nickname' => $nickname,
        ]));

        return self::retrieve($id);
    }

    /**
     * @throws ModelNotFoundException
     */
    public static function retrieve(string $identifier): static
    {
        /** @var self $model */
        $model = self::query()->findOrFail($identifier);
        return $model;
    }

    public static function findByEmail(string $email): static
    {
        /** @var self $model */
        $model = self::query()->where('email', $email)->firstOrFail();
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
        if ($role->value === $this->role) {
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
        event(new UserPasswordChanged($this->id, bcrypt($password)));
    }

    public function changeRememberToken(string $rememberToken): void
    {
        event(new UserRememberTokenChanged($this->id, $rememberToken));
    }

    public function changeNickname(?string $nickname):void
    {
        if ($nickname !== $this->nickname) {
            event(new UserNicknameChanged($this->id, $nickname));
        }
    }

    public function getAvatar($size = 128): string
    {
        $hash = md5(strtolower(trim($this->email)));

        return "https://www.gravatar.com/avatar/{$hash}?s={$size}";
    }

    public function savedTracks()
    {
        return $this->morphedByMany(Track::class, 'saveable');
    }

    public function hasSavedTrack(string $id): bool
    {
        return $this->savedTracks()->where('saveable_id', $id)->exists();
    }
}
