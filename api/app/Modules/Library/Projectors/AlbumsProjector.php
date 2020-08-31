<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\Albums\{AlbumArtworkChanged,
    AlbumCreated,
    AlbumDeleted,
    AlbumTitleChanged,
    AlbumYearChanged};
use App\Modules\Library\Models\{Album, Reciter};
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class AlbumsProjector extends Projector
{
    public function onAlbumCreated(AlbumCreated $event): void
    {
        $reciter = Reciter::retrieve($event->reciterId);

        $data = collect($event->attributes);
        $data->put('id', $event->id);
        $data->put('reciter_id', $reciter->id);

        $album = new Album($data->all());

        $reciter->albums()->save($album);
    }

    public function onAlbumTitleChanged(AlbumTitleChanged $event): void
    {
        $album = Album::retrieve($event->id);
        $album->title = $event->title;
        $album->saveOrFail();
    }

    public function onAlbumYearChanged(AlbumYearChanged $event): void
    {
        $album = Album::retrieve($event->id);
        $album->year = $event->year;
        $album->saveOrFail();
    }

    public function onAlbumArtworkChanged(AlbumArtworkChanged $event): void
    {
        $album = Album::retrieve($event->id);
        $album->artwork = $event->artwork;
        $album->saveOrFail();
    }

    public function onAlbumDeleted(AlbumDeleted $event): void
    {
        $album = Album::retrieve($event->id);
        $album->deleteAlbum();
    }

    public function resetState(): void
    {
        Album::truncate();
    }
}
