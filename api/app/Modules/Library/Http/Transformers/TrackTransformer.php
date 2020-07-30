<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Transformers;

use App\Enum\MediaType;
use App\Http\Transformers\Transformer;
use App\Modules\Library\Models\Track;
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
            'id' => $track->id,
            'reciterId' => $track->reciter_id,
            'albumId' => $track->album_id,
            'title' => $track->title,
            'slug' => $track->slug,
            'year' => $track->album->year,
            $this->timestamps($track),
        ];
    }

    public function includeReciter(Track $track): Item
    {
        return $this->item($track->reciter, new ReciterTransformer());
    }

    public function includeAlbum(Track $track): Item
    {
        return $this->item($track->album, new AlbumTransformer());
    }

    public function includeLyrics(Track $track): ?ResourceInterface
    {
        if (($lyrics = $track->lyrics) === null) {
            return $this->null();
        }

        return $this->item($lyrics, new LyricsTransformer());
    }

    public function includeMedia(Track $track): Collection
    {
        return $this->collection(collect([$track->audio])->filter(), new MediaTransformer());
    }

    public function includeRelated(Track $track): Primitive
    {
        return $this->primitive([
            'lyrics' => $track->lyrics !== null,
            'audio' => $track->audio !== null,
        ]);
    }
}
