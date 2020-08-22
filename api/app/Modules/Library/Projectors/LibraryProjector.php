<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Entities\Album as AlbumEntity;
use App\Modules\Library\Events\Albums\AlbumArtworkChanged;
use App\Modules\Library\Events\Albums\AlbumCreated;
use App\Modules\Library\Events\Albums\AlbumDeleted;
use App\Modules\Library\Events\Albums\AlbumTitleChanged;
use App\Modules\Library\Events\Albums\AlbumYearChanged;
use App\Modules\Library\Events\Tracks\TrackAudioChanged;
use App\Modules\Library\Events\Tracks\TrackCreated;
use App\Modules\Library\Events\Tracks\TrackDeleted;
use App\Modules\Library\Events\Tracks\TrackLyricsChanged;
use App\Modules\Library\Events\Tracks\TrackTitleChanged;
use App\Modules\Library\Models\Album as AlbumModel;
use App\Modules\Library\Models\Track as TrackModel;
use App\Modules\Library\Entities\Track as TrackEntity;
use App\Modules\Library\Repositories\LibraryAggregateRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    private LibraryAggregateRepository $repository;

    public function __construct(LibraryAggregateRepository $repository)
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

    public function onTrackCreated(TrackCreated $event): void
    {
        $reciter = $this->repository->retrieveByAlbumId($event->albumId);
        $album = $reciter->getAlbum($event->albumId);

        $data = collect($event->attributes);

        $track = new TrackEntity(
            $event->id,
            $data->get('title'),
        );

        $album->addTrack($track);
    }

    public function onTrackTitleChanged(TrackTitleChanged $event): void
    {
        try {
            $reciter = $this->repository->retrieveByTrackId($event->id);
            $track = $reciter->getTrack($event->id);

            $track->title = $event->title;
        } catch (ModelNotFoundException $e) {
            //
        }
    }

    public function onTrackDeleted(TrackDeleted $event): void
    {
        try {
            $reciter = $this->repository->retrieveByTrackId($event->id);
            $reciter->removeTrack($event->id);
        } catch (ModelNotFoundException $e) {
            // Doesn't exist, so no worries.
        }
    }

    public function onTrackLyricsChanged(TrackLyricsChanged $event): void
    {
        try {
            $reciter = $this->repository->retrieveByTrackId($event->id);
            $track = $reciter->getTrack($event->id);
            $track->lyrics = $event->document;
        } catch (ModelNotFoundException $e) {
            //
        }
    }

    public function onTrackAudioChanged(TrackAudioChanged $event): void
    {
        try {
            $reciter = $this->repository->retrieveByTrackId($event->id);
            $track = $reciter->getTrack($event->id);
            $track->audio = $event->path;
        } catch (ModelNotFoundException $e) {
            //
        }
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
