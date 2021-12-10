<?php

declare(strict_types=1);

namespace App\Modules\Audit\Projectors;

use App\Modules\Core\Events\InteractsWithStoredEvents;
use App\Modules\Audit\Enum\{ChangeType, EntityType};
use App\Modules\Audit\Exceptions\RevisionNotFoundException;
use App\Modules\Audit\Models\Revision;
use App\Modules\Audit\Snapshots\{AlbumSnapshot, TrackSnapshot};
use App\Modules\Library\Events\Albums\{AlbumArtworkChanged,
    AlbumCreated,
    AlbumDeleted,
    AlbumEvent,
    AlbumTitleChanged,
    AlbumYearChanged};
use Spatie\EventSourcing\StoredEvents\StoredEvent;
use App\Modules\Library\Events\Tracks\{TrackCreated, TrackDeleted};
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class AlbumRevisionsProjector extends Projector
{
    use InteractsWithStoredEvents;

    public function onAlbumCreated(AlbumCreated $event): void
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
            ChangeType::CREATED,
            $event->getUserId(),
            $this->getStoredEvent($event)
        );

        $revision->save();
    }

    public function onAlbumTitleChanged(AlbumTitleChanged $event): void
    {
        $this->recordModification(
            $event,
            $this->getStoredEvent($event),
            fn (AlbumSnapshot $snapshot) => $snapshot->title = $event->title
        );
    }

    public function onAlbumYearChanged(AlbumYearChanged $event): void
    {
        $this->recordModification(
            $event,
            $this->getStoredEvent($event),
            fn (AlbumSnapshot $snapshot) => $snapshot->year = $event->year
        );
    }

    public function onAlbumArtworkChanged(AlbumArtworkChanged $event): void
    {
        $this->recordModification(
            $event,
            $this->getStoredEvent($event),
            fn (AlbumSnapshot $snapshot) => $snapshot->artwork = $event->artwork
        );
    }

    public function onAlbumDeleted(AlbumDeleted $event): void
    {
        $last = $this->getLastRevision($event->id);

        $last->reviseForDeletion(
            $event->getUserId(),
            $this->getStoredEvent($event),
        )->save();
    }

    public function onTrackCreated(TrackCreated $event): void
    {
        $last = $this->getLastRevision($event->albumId);
        $snapshot = AlbumSnapshot::fromRevision($last);
        $snapshot->tracks->push($event->id);

        $last->revise(
            $snapshot,
            ChangeType::MODIFIED,
            $event->getUserId(),
            $this->getStoredEvent($event),
        )->save();
    }

    public function onTrackDeleted(TrackDeleted $event): void
    {
        $trackRevision = Revision::getLast(EntityType::TRACK, $event->id);
        $trackSnapshot = TrackSnapshot::fromRevision($trackRevision);

        $last = $this->getLastRevision($trackSnapshot->albumId);
        $snapshot = AlbumSnapshot::fromRevision($last);
        $snapshot->tracks = $snapshot->tracks->filter(fn ($id) => $id !== $trackSnapshot->id);

        $last->revise(
            $snapshot,
            ChangeType::MODIFIED,
            $event->getUserId(),
            $this->getStoredEvent($event),
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
            ChangeType::MODIFIED,
            $event->getUserId(),
            $storedEvent
        )->save();
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
