<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Data\Reciter as ReciterEntity;
use App\Modules\Library\Events\Albums\AlbumArtworkChanged;
use App\Modules\Library\Events\Albums\AlbumCreated;
use App\Modules\Library\Events\Albums\AlbumDeleted;
use App\Modules\Library\Events\Albums\AlbumTitleChanged;
use App\Modules\Library\Events\Albums\AlbumYearChanged;
use App\Modules\Library\Events\Reciters\{ReciterAvatarChanged,
    ReciterCreated,
    ReciterDeleted,
    ReciterDescriptionChanged,
    ReciterNameChanged};
use App\Modules\Library\Events\Tracks\TrackAudioChanged;
use App\Modules\Library\Events\Tracks\TrackCreated;
use App\Modules\Library\Events\Tracks\TrackDeleted;
use App\Modules\Library\Events\Tracks\TrackLyricsChanged;
use App\Modules\Library\Events\Tracks\TrackTitleChanged;
use App\Modules\Library\Models\Album as AlbumModel;
use App\Modules\Library\Models\Reciter as ReciterModel;
use App\Modules\Library\Models\Track as TrackModel;
use App\Modules\Library\Repositories\LibraryAggregateRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        $reciter->applyReciterNameChanged($event);
    }

    public function onReciterDescriptionChanged(ReciterDescriptionChanged $event): void
    {
        $reciter = $this->repository->retrieve($event->id);
        $reciter->applyReciterDescriptionChanged($event);
    }

    public function onReciterAvatarChanged(ReciterAvatarChanged $event): void
    {
        $reciter = $this->repository->retrieve($event->id);
        $reciter->applyReciterAvatarChanged($event);
    }

    public function onReciterDeleted(ReciterDeleted $event): void
    {
        $this->repository->remove($event->id);
    }

    public function onAlbumCreated(AlbumCreated $event): void
    {
        $reciter = $this->repository->retrieve($event->reciterId);
        $reciter->applyAlbumCreated($event);
    }

    public function onAlbumTitleChanged(AlbumTitleChanged $event): void
    {
        $reciter = $this->repository->retrieveByAlbumId($event->id);
        $reciter->applyAlbumTitleChanged($event);
    }

    public function onAlbumYearChanged(AlbumYearChanged $event): void
    {
        $reciter = $this->repository->retrieveByAlbumId($event->id);
        $reciter->applyAlbumYearChanged($event);
    }

    public function onAlbumArtworkChanged(AlbumArtworkChanged $event): void
    {
        $reciter = $this->repository->retrieveByAlbumId($event->id);
        $reciter->applyAlbumArtworkChanged($event);
    }

    public function onAlbumDeleted(AlbumDeleted $event): void
    {
        $reciter = $this->repository->retrieveByAlbumId($event->id);

        $reciter->removeAlbum($event->id);
    }

    public function onTrackCreated(TrackCreated $event): void
    {
        $reciter = $this->repository->retrieveByAlbumId($event->albumId);
        $reciter->applyTrackCreated($event);
    }

    public function onTrackTitleChanged(TrackTitleChanged $event): void
    {
        try {
            $reciter = $this->repository->retrieveByTrackId($event->id);
            $reciter->applyTrackTitleChanged($event);
        } catch (ModelNotFoundException $e) {
            //
        }
    }

    public function onTrackDeleted(TrackDeleted $event): void
    {
        try {
            $reciter = $this->repository->retrieveByTrackId($event->id);

        } catch (ModelNotFoundException $e) {
            // Doesn't exist, so no worries.
        }
    }

    public function onTrackLyricsChanged(TrackLyricsChanged $event): void
    {
        try {
            $reciter = $this->repository->retrieveByTrackId($event->id);
            $reciter->applyTrackLyricsChanged($event);
        } catch (ModelNotFoundException $e) {
            //
        }
    }

    public function onTrackAudioChanged(TrackAudioChanged $event): void
    {
        try {
            $reciter = $this->repository->retrieveByTrackId($event->id);
            $reciter->applyTrackAudioChanged($event);
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
