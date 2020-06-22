<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Entities\Contracts\{Entity, TimestampedEntity};
use App\Enum\Role;
use Illuminate\Contracts\Auth\Authenticatable;
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class User implements Entity, TimestampedEntity, Authenticatable
{
    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private string $name;
    private Role $role;
    private string $email;
    private ?string $password;
    private ?string $nickname;
    private ?string $rememberToken;

    public function __construct(Role $role, string $name, string $email)
    {
        $this->id = Uuid::uuid1();
        $this->role = $role;
        $this->name = $name;
        $this->email = $email;
        $this->password = null;
        $this->rememberToken = null;
        $this->nickname = null;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    /**
     * Get the column name for the primary key
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifier()
    {
        return $this->getId();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->rememberToken;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     */
    public function setRememberToken($value)
    {
        $this->rememberToken = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'rememberToken';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = bcrypt($password);
    }

    public function getAvatar($size = 128): string
    {
        $hash = md5(strtolower(trim($this->email)));

        return "https://www.gravatar.com/avatar/{$hash}?s={$size}";
    }
}
