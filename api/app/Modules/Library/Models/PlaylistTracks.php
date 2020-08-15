<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class PlaylistTracks extends Model
{
    protected $guarded = [];

    public static function create(string $playlistId, string $trackId): self
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
}
