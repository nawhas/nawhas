<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use App\Modules\Authentication\Models\User;
use App\Modules\Library\Events\Playlists\PlaylistCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

class Playlist extends Model
{
    protected $fillable = ['name'];

    public static function create(string $name, string $trackId): self
    {
        $id = Uuid::uuid1()->toString();

        event(new PlaylistCreated($id,[
            'name' => $name
        ]));

        PlaylistTracks::create($id, $trackId);

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
