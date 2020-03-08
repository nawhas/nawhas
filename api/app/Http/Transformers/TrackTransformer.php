<?php

declare(strict_types=1);

namespace App\Http\Transformers;

use App\Entities\Track;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Primitive;
use League\Fractal\Resource\ResourceInterface;

class TrackTransformer extends Transformer
{
    protected $availableIncludes = ['reciter', 'album', 'lyrics', 'media', 'related'];

    public function toArray(Track $track): array
    {
        return [
            'id' => $track->getId(),
            'reciterId' => $track->getReciter()->getId(),
            'albumId' => $track->getAlbum()->getId(),
            'title' => $track->getTitle(),
            'slug' => $track->getSlug(),
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

    public function includeLyrics(Track $track): ?ResourceInterface
    {
        if (($lyrics = $track->getLyrics()) === null) {
            return $this->null();
        }

        return $this->item($lyrics, new LyricsTransformer());
    }

    public function includeMedia(Track $track): Collection
    {
        return $this->collection($track->getMedia(), new MediaTransformer());
    }

    public function includeRelated(Track $track): Primitive
    {
        return $this->primitive([
            'lyrics' => $track->hasLyrics(),
            'audio' => $track->hasAudioFile(),
        ]);
    }
}
