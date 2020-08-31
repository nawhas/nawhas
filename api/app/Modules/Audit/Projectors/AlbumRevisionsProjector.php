<?php

declare(strict_types=1);

namespace App\Modules\Audit\Projectors;

use App\Modules\Audit\Enum\{ChangeType, EntityType};
use App\Modules\Audit\Exceptions\RevisionNotFoundException;
use App\Modules\Audit\Models\Revision;
use App\Modules\Audit\Snapshots\{AlbumSnapshot, ReciterSnapshot, TrackSnapshot};
use App\Modules\Library\Events\Albums\{AlbumArtworkChanged,
    AlbumCreated,
    AlbumDeleted,
    AlbumEvent,
    AlbumTitleChanged,
    AlbumYearChanged};
use App\Modules\Library\Events\Tracks\{TrackCreated, TrackDeleted};
use Carbon\Carbon;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class AlbumRevisionsProjector extends Projector
{
    public function onAlbumCreated(AlbumCreated $event, StoredEvent $storedEvent): void
    {
        $data = collect($event->attributes);
        $snapshot = new AlbumSnapshot(
            $event->id,
            $event->reciterId,
            $data->get('title'),
            $data->get('year'),
            $data->get('artwork'),
        );

        $revision = Revision::makeInitial(
            $snapshot,
            ChangeType::CREATED(),
            $event->getUserId(),
            Carbon::make($storedEvent->created_at),
        );

        $revision->save();
    }

    public function onAlbumTitleChanged(AlbumTitleChanged $event, StoredEvent $storedEvent): void
    {
        $this->recordModification(
            $event,
            $storedEvent,
            fn (AlbumSnapshot $snapshot) => $snapshot->title = $event->title
        );
    }

    public function onAlbumYearChanged(AlbumYearChanged $event, StoredEvent $storedEvent): void
    {
        $this->recordModification(
            $event,
            $storedEvent,
            fn (AlbumSnapshot $snapshot) => $snapshot->year = $event->year
        );
    }

    public function onAlbumArtworkChanged(AlbumArtworkChanged $event, StoredEvent $storedEvent): void
    {
        $this->recordModification(
            $event,
            $storedEvent,
            fn (AlbumSnapshot $snapshot) => $snapshot->artwork = $event->artwork
        );
    }

    public function onAlbumDeleted(AlbumDeleted $event, StoredEvent $storedEvent): void
    {
        $last = $this->getLastRevision($event->id);

        $last->reviseForDeletion(
            $event->getUserId(),
            Carbon::make($storedEvent->created_at),
        )->save();
    }

    public function onTrackCreated(TrackCreated $event, StoredEvent $storedEvent): void
    {
        $last = $this->getLastRevision($event->albumId);
        $snapshot = AlbumSnapshot::fromRevision($last);
        $snapshot->tracks->push($event->id);

        $last->revise(
            $snapshot,
            ChangeType::MODIFIED(),
            $event->getUserId(),
            Carbon::make($storedEvent->created_at),
        )->save();
    }

    public function onTrackDeleted(TrackDeleted $event, StoredEvent $storedEvent): void
    {
        $trackRevision = Revision::getLast(EntityType::TRACK, $event->id);
        $trackSnapshot = TrackSnapshot::fromRevision($trackRevision);

        $last = $this->getLastRevision($trackSnapshot->albumId);
        $snapshot = ReciterSnapshot::fromRevision($last);
        $snapshot->albums = $snapshot->albums->filter(fn ($id) => $id !== $trackSnapshot->id);

        $last->revise(
            $snapshot,
            ChangeType::MODIFIED(),
            $event->getUserId(),
            Carbon::make($storedEvent->created_at),
        );
    }

    public function resetState(): void
    {
        Revision::where('entity_type', EntityType::ALBUM)->delete();
    }

    private function recordModification(AlbumEvent $event, StoredEvent $storedEvent, callable $modify): void
    {
        $last = $this->getLastRevision($event->id);
        $snapshot = AlbumSnapshot::fromRevision($last);

        $modify($snapshot);

        $last->revise(
            $snapshot,
            ChangeType::MODIFIED(),
            $event->getUserId(),
            Carbon::make($storedEvent->created_at)
        );
    }

    private function getLastRevision(string $id): Revision
    {
        $last = Revision::getLast(EntityType::ALBUM, $id);

        if ($last === null) {
            throw RevisionNotFoundException::forEntity(EntityType::ALBUM, $id);
        }

        return $last;
    }
}
