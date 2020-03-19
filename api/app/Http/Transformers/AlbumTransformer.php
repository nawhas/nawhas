<?php

declare(strict_types=1);

namespace App\Http\Transformers;

use App\Entities\Album;
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
            'id' => $album->getId(),
            'reciterId' => $album->getReciter()->getId(),
            'title' => $album->getTitle(),
            'year' => $album->getYear(),
            'artwork' => $album->getArtworkUrl(),
            $this->timestamps($album),
        ];
    }

    public function includeReciter(Album $album): Item
    {
        return $this->item($album->getReciter(), new ReciterTransformer());
    }

    public function includeTracks(Album $album): Collection
    {
        return $this->collection($album->getTracks(), new TrackTransformer());
    }

    public function includeRelated(Album $album): Primitive
    {
        return $this->primitive([
           'tracks' => $album->getTracks()->count(),
        ]);
    }
}
