<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use App\Modules\Authentication\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

class Playlist extends Model
{
    public static function create(string $userId, string $name): self
    {
        $id = Uuid::uuid1()->toString();

        // TODO - Need to trigger an event to create the playlist record

        return self::retrieve($id);
    }

    public static function retrieve(string $identifier): self
    {
        /** @var self $model */
        $model = self::query()->findOrFail($identifier);
        return $model;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(PlaylistTracks::class);
    }
}
