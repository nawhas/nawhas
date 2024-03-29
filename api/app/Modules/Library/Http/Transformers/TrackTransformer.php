<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Transformers;

use App\Modules\Core\Transformers\Transformer;
use App\Modules\Library\Models\Track;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Primitive;
use League\Fractal\Resource\ResourceInterface;

class TrackTransformer extends Transformer
{
    protected array $availableIncludes = ['reciter', 'album', 'lyrics', 'media', 'related'];

    public function toArray(Track $track): array
    {
        return [
            'id' => $track->id,
            'reciterId' => $track->reciter_id,
            'albumId' => $track->album_id,
            'title' => $track->title,
            'slug' => $track->slug,
            'year' => $track->album->year,
            'audio' => $track->audio,
            'video' => $track->video,
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
            return $this->empty();
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
            'video' => $track->video !== null,
        ]);
    }
}
