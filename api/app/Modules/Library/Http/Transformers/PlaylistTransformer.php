<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Transformers;

use App\Modules\Authentication\Http\Transformers\UserTransformer;
use App\Modules\Core\Transformers\Transformer;
use App\Modules\Library\Models\Playlist;
use League\Fractal\Resource\Collection;

class PlaylistTransformer extends Transformer
{
    protected $availableIncludes = [
        'user', 'tracks'
    ];

    public function toArray(Playlist $playlist): array
    {
        return [
            'id' => $playlist->id,
            'name' => $playlist->name,
            'tracks' => $this->collection($playlist->tracks, new TrackTransformer())
        ];
    }

    public function includeUser(Playlist $playlist): Collection
    {
        return $this->collection($playlist->user, new UserTransformer());
    }
}
