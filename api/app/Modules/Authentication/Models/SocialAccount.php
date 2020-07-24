<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Models;

use App\Entities\Contracts\TimestampedEntity;
use App\Modules\Authentication\Events\SocialAccountProviderIdChanged;
use App\Modules\Authentication\Events\SocialAccountRegistered;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class SocialAccount extends Model implements TimestampedEntity
{
    protected $keyType = 'string';

    public static function create(string $user_id, string $provider, string $providerId): self
    {
        $id = Uuid::uuid1()->toString();

        event(new SocialAccountRegistered($id, [
            'user_id' => $user_id,
            'provider' => $provider,
            'providerId' => $providerId,
        ]));

        return self::retrieve($id);
    }

    /**
     * @param string $identifier
     * @throws ModelNotFoundException
     * @return SocialAccount
     */
    public static function retrieve(string $identifier): self
    {
        /** @var self $model */
        $model = self::query()->findOrFail($identifier);
        return $model;
    }

    /**
     * @throws ModelNotFoundException
     */
    public static function findByProviderId(string $provider, string $providerId): self
    {
        /** @var self $model */
        $model = self::query()->where('provider', $provider)->where('provider_id', $providerId)->firstOrFail();
        return $model;
    }

    public function changeProviderId(string $providerId): void
    {
        if ($providerId !== $this->providerId) {
            event(new SocialAccountProviderIdChanged($this->id, $providerId));
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
