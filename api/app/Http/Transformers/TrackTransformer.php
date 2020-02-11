<?php

declare(strict_types=1);

namespace App\Http\Transformers;

use App\Entities\Track;
use League\Fractal\Resource\Item;

class TrackTransformer extends Transformer
{
    public function toArray(Track $track): array
    {
        return [
            'id' => $track->getId(),
            'reciterId' => $track->getReciter()->getId(),
            'albumId' => $track->getAlbum()->getId(),
            'title' => $track->getTitle(),
            'year' => $track->getAlbum()->getYear(),
            $this->timestamps($track),
        ];
    }

    public function includeReciter(Track $track): Item
    {
        return $this->item($track->getReciter(), new ReciterTransformer());
    }

    public function includeAlbum(Track $track): Item
    {
        return $this->item($track->getAlbum(), new AlbumTransformer());
    }
}
