<?php

declare(strict_types=1);

namespace App\Modules\Audit\Projectors;

use App\Modules\Core\Events\InteractsWithStoredEvents;
use App\Modules\Audit\Enum\{ChangeType, EntityType};
use App\Modules\Audit\Exceptions\RevisionNotFoundException;
use App\Modules\Audit\Models\Revision;
use App\Modules\Audit\Snapshots\AlbumSnapshot;
use App\Modules\Audit\Snapshots\ReciterSnapshot;
use App\Modules\Library\Events\Albums\{AlbumCreated, AlbumDeleted};
use App\Modules\Library\Events\Reciters\{ReciterAvatarChanged,
    ReciterCreated,
    ReciterDeleted,
    ReciterDescriptionChanged,
    ReciterEvent,
    ReciterNameChanged};
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class ReciterRevisionsProjector extends Projector
{
    use InteractsWithStoredEvents;

    public function onReciterCreated(ReciterCreated $event): void
    {
        $data = collect($event->attributes);
        $snapshot = new ReciterSnapshot(
            $event->id,
            $data->get('name'),
            $data->get('description'),
            $data->get('avatar'),
        );

        $revision = Revision::makeInitial(
            $snapshot,
            ChangeType::CREATED,
            $event->getUserId(),
            $this->getStoredEvent($event),
        );

        $revision->save();
    }

    public function onReciterNameChanged(ReciterNameChanged $event): void
    {
        $this->recordModification(
            $event,
            $this->getStoredEvent($event),
            fn (ReciterSnapshot $snapshot) => $snapshot->name = $event->name
        );
    }

    public function onReciterDescriptionChanged(ReciterDescriptionChanged $event): void
    {
        $this->recordModification(
            $event,
            $this->getStoredEvent($event),
            fn (ReciterSnapshot $snapshot) => $snapshot->description = $event->description
        );
    }

    public function onReciterAvatarChanged(ReciterAvatarChanged $event): void
    {
        $this->recordModification(
            $event,
            $this->getStoredEvent($event),
            fn (ReciterSnapshot $snapshot) => $snapshot->avatar = $event->avatar
        );
    }

    public function onReciterDeleted(ReciterDeleted $event): void
    {
        $last = $this->getLastRevision($event->id);

        $last->reviseForDeletion(
            $event->getUserId(),
            $this->getStoredEvent($event),
        )->save();
    }

    public function onAlbumCreated(AlbumCreated $event): void
    {
        $last = $this->getLastRevision($event->reciterId);
        $snapshot = ReciterSnapshot::fromRevision($last);
        $snapshot->albums->push($event->id);

        $last->revise(
            $snapshot,
            ChangeType::MODIFIED,
            $event->getUserId(),
            $this->getStoredEvent($event),
        )->save();
    }

    public function onAlbumDeleted(AlbumDeleted $event): void
    {
        $albumRevision = Revision::getLast(EntityType::ALBUM, $event->id);
        $albumSnapshot = AlbumSnapshot::fromRevision($albumRevision);

        $last = $this->getLastRevision($albumSnapshot->reciterId);
        $snapshot = ReciterSnapshot::fromRevision($last);
        $snapshot->albums = $snapshot->albums->filter(fn ($id) => $id !== $albumSnapshot->id);

        $last->revise(
            $snapshot,
            ChangeType::MODIFIED,
            $event->getUserId(),
            $this->getStoredEvent($event),
        );
    }

    public function resetState(): void
    {
        Revision::where('entity_type', EntityType::RECITER)->delete();
    }

    private function getLastRevision(string $id): Revision
    {
        $last = Revision::getLast(EntityType::RECITER, $id);

        if ($last === null) {
            throw RevisionNotFoundException::forEntity(EntityType::RECITER, $id);
        }

        return $last;
    }

    private function recordModification(ReciterEvent $event, StoredEvent $storedEvent, callable $modify): void
    {
        $last = $this->getLastRevision($event->id);
        $snapshot = ReciterSnapshot::fromRevision($last);

        $modify($snapshot);

        $last->revise(
            $snapshot,
            ChangeType::MODIFIED,
            $event->getUserId(),
            $storedEvent
        )->save();
    }
}
