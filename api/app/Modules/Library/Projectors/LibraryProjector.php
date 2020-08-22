<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Entities\Album;
use App\Modules\Library\Entities\Album as AlbumEntity;
use App\Modules\Library\Events\Albums\AlbumArtworkChanged;
use App\Modules\Library\Events\Albums\AlbumCreated;
use App\Modules\Library\Events\Albums\AlbumDeleted;
use App\Modules\Library\Events\Albums\AlbumTitleChanged;
use App\Modules\Library\Events\Albums\AlbumYearChanged;
use App\Modules\Library\Models\Album as AlbumModel;
use App\Modules\Library\Models\Track as TrackModel;
use App\Modules\Library\Repositories\ReciterRepository;
use App\Modules\Library\Events\Reciters\{
    ReciterAvatarChanged,
    ReciterCreated,
    ReciterDeleted,
    ReciterDescriptionChanged,
    ReciterNameChanged
};
use App\Modules\Library\Models\Reciter as ReciterModel;
use App\Modules\Library\Entities\Reciter as ReciterEntity;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class LibraryProjector extends Projector
{
    private ReciterRepository $repository;

    public function __construct(ReciterRepository $repository)
    {
        $this->repository = $repository;
    }

    public function onReciterCreated(ReciterCreated $event): void
    {
        $data = collect($event->attributes);

        $reciter = new ReciterEntity(
            $event->id,
            $data->get('name'),
            $data->get('description'),
            $data->get('avatar'),
        );

        $this->repository->persist($reciter);
    }

    public function onReciterNameChanged(ReciterNameChanged $event): void
    {
        $reciter = $this->repository->retrieve($event->id);
        $reciter->name = $event->name;
        $this->repository->persist($reciter);
    }

    public function onReciterDescriptionChanged(ReciterDescriptionChanged $event): void
    {
        $reciter = $this->repository->retrieve($event->id);
        $reciter->description = $event->description;
        $this->repository->persist($reciter);
    }

    public function onReciterAvatarChanged(ReciterAvatarChanged $event): void
    {
        $reciter = $this->repository->retrieve($event->id);
        $reciter->avatar = $event->avatar;
        $this->repository->persist($reciter);
    }

    public function onReciterDeleted(ReciterDeleted $event): void
    {
        $this->repository->remove($event->id);
    }

    public function onAlbumCreated(AlbumCreated $event): void
    {
        $reciter = $this->repository->retrieve($event->reciterId);

        $data = collect($event->attributes);

        $album = new AlbumEntity(
            $event->id,
            $data->get('title'),
            $data->get('year'),
            $data->get('artwork'),
        );

        $reciter->addAlbum($album);
        $this->repository->persist($reciter);
    }

    public function onAlbumTitleChanged(AlbumTitleChanged $event): void
    {
        $reciter = $this->repository->retrieveByAlbumId($event->id);

        $album = $reciter->getAlbum($event->id);
        $album->title = $event->title;
    }

    public function onAlbumYearChanged(AlbumYearChanged $event): void
    {
        $reciter = $this->repository->retrieveByAlbumId($event->id);

        $album = $reciter->getAlbum($event->id);
        $album->year = $event->year;
    }

    public function onAlbumArtworkChanged(AlbumArtworkChanged $event): void
    {
        $reciter = $this->repository->retrieveByAlbumId($event->id);

        $album = $reciter->getAlbum($event->id);
        $album->artwork = $event->artwork;
    }

    public function onAlbumDeleted(AlbumDeleted $event): void
    {
        $reciter = $this->repository->retrieveByAlbumId($event->id);

        $reciter->removeAlbum($event->id);
    }

    public function resetState(): void
    {
        ReciterModel::truncate();
        AlbumModel::truncate();
        TrackModel::truncate();
    }

    public function onFinishedEventReplay(): void
    {
        $this->repository->flush();
    }
}
