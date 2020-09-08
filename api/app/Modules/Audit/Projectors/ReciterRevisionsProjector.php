<?php

declare(strict_types=1);

namespace App\Modules\Audit\Projectors;

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
    public function onReciterCreated(ReciterCreated $event, StoredEvent $storedEvent): void
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
            ChangeType::CREATED(),
            $event->getUserId(),
            $storedEvent,
        );

        $revision->save();
    }

    public function onReciterNameChanged(ReciterNameChanged $event, StoredEvent $storedEvent): void
    {
        $this->recordModification(
            $event,
            $storedEvent,
            fn (ReciterSnapshot $snapshot) => $snapshot->name = $event->name
        );
    }

    public function onReciterDescriptionChanged(ReciterDescriptionChanged $event, StoredEvent $storedEvent): void
    {
        $this->recordModification(
            $event,
            $storedEvent,
            fn (ReciterSnapshot $snapshot) => $snapshot->description = $event->description
        );
    }

    public function onReciterAvatarChanged(ReciterAvatarChanged $event, StoredEvent $storedEvent): void
    {
        $this->recordModification(
            $event,
            $storedEvent,
            fn (ReciterSnapshot $snapshot) => $snapshot->avatar = $event->avatar
        );
    }

    public function onReciterDeleted(ReciterDeleted $event, StoredEvent $storedEvent): void
    {
        $last = $this->getLastRevision($event->id);

        $last->reviseForDeletion(
            $event->getUserId(),
            $storedEvent,
        )->save();
    }

    public function onAlbumCreated(AlbumCreated $event, StoredEvent $storedEvent): void
    {
        $last = $this->getLastRevision($event->reciterId);
        $snapshot = ReciterSnapshot::fromRevision($last);
        $snapshot->albums->push($event->id);

        $last->revise(
            $snapshot,
            ChangeType::MODIFIED(),
            $event->getUserId(),
            $storedEvent,
        )->save();
    }

    public function onAlbumDeleted(AlbumDeleted $event, StoredEvent $storedEvent): void
    {
        $albumRevision = Revision::getLast(EntityType::ALBUM(), $event->id);
        $albumSnapshot = AlbumSnapshot::fromRevision($albumRevision);

        $last = $this->getLastRevision($albumSnapshot->reciterId);
        $snapshot = ReciterSnapshot::fromRevision($last);
        $snapshot->albums = $snapshot->albums->filter(fn ($id) => $id !== $albumSnapshot->id);

        $last->revise(
            $snapshot,
            ChangeType::MODIFIED(),
            $event->getUserId(),
            $storedEvent,
        );
    }

    public function resetState(): void
    {
        Revision::where('entity_type', EntityType::RECITER)->delete();
    }

    private function getLastRevision(string $id): Revision
    {
        $last = Revision::getLast(EntityType::RECITER(), $id);

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
            ChangeType::MODIFIED(),
            $event->getUserId(),
            $storedEvent
        )->save();
    }
}
