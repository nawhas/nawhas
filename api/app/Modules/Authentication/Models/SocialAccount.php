<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Models;

use App\Entities\Contracts\TimestampedEntity;
use App\Modules\Authentication\Events\SocialAccountProviderIdChanged;
use App\Modules\Authentication\Events\SocialAccountRegistered;
use App\Modules\Core\Models\HasTimestamps;
use App\Modules\Core\Models\HasUuid;
use App\Modules\Core\Models\UsesDataConnection;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class SocialAccount extends Model implements TimestampedEntity
{
    use HasTimestamps;
    use HasUuid;
    use UsesDataConnection;

    public static function create(string $userId, string $provider, string $providerId): self
    {
        $id = Uuid::uuid1()->toString();

        event(new SocialAccountRegistered($id, [
            'userId' => $userId,
            'provider' => $provider,
            'providerId' => $providerId,
        ]));

        return self::retrieve($id);
    }

    /**
     * @throws ModelNotFoundException
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
