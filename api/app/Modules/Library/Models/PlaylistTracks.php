<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use App\Modules\Library\Events\Playlists\PlaylistTrackAdded;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class PlaylistTracks extends Model
{
    protected $fillable = ['playlistId', 'trackId'];

    public static function create(string $playlistId, string $trackId): self
    {
        $id = Uuid::uuid1()->toString();

        event(new PlaylistTrackAdded($id, [
            'playlistId' => $playlistId,
            'trackId' => $trackId,
        ]));

        return self::retrieve($id);
    }

    public static function retrieve(string $identifier): self
    {
        /** @var self $model */
        $model = self::query()->findOrFail($identifier);
        return $model;
    }
}
