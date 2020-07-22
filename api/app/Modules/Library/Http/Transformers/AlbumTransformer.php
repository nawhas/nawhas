<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Transformers;

use App\Http\Transformers\TrackTransformer;
use App\Http\Transformers\Transformer;
use App\Modules\Library\Models\Album;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Primitive;

class AlbumTransformer extends Transformer
{
    protected $availableIncludes = [
        'reciter', 'tracks', 'related'
    ];

    public function toArray(Album $album): array
    {
        return [
            'id' => $album->id,
            'reciterId' => $album->reciter_id,
            'title' => $album->title,
            'year' => $album->year,
            'artwork' => $album->artwork ? Storage::url($album->artwork) : null,
            $this->timestamps($album),
        ];
    }

    public function includeReciter(Album $album): Item
    {
        return $this->item($album->reciter, new ReciterTransformer());
    }

    public function includeTracks(Album $album): Collection
    {
        return $this->collection($album->tracks, new TrackTransformer());
    }

    public function includeRelated(Album $album): Primitive
    {
        return $this->primitive([
            'tracks' => $album->tracks()->count(),
        ]);
    }
}
