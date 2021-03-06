<?php

declare(strict_types=1);

namespace App\Modules\Audit\Projectors;

use App\Modules\Audit\Enum\{ChangeType, EntityType};
use App\Modules\Audit\Exceptions\RevisionNotFoundException;
use App\Modules\Audit\Models\Revision;
use App\Modules\Audit\Snapshots\TrackSnapshot;
use App\Modules\Library\Events\Tracks\{TrackAudioChanged,
    TrackCreated,
    TrackDeleted,
    TrackEvent,
    TrackLyricsChanged,
    TrackTitleChanged,
    TrackVideoChanged};
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class TrackRevisionsProjector extends Projector
{
    public function onTrackCreated(TrackCreated $event, StoredEvent $storedEvent): void
    {
        $data = collect($event->attributes);
        $snapshot = new TrackSnapshot(
            $event->id,
            $event->albumId,
            $data->get('title'),
            $data->get('audio'),
        );

        $revision = Revision::makeInitial(
            $snapshot,
            ChangeType::CREATED(),
            $event->getUserId(),
            $storedEvent
        );

        $revision->save();
    }

    public function onTrackTitleChanged(TrackTitleChanged $event, StoredEvent $storedEvent): void
    {
        $this->recordModification(
            $event,
            $storedEvent,
            fn (TrackSnapshot $snapshot) => $snapshot->title = $event->title
        );
    }

    public function onTrackLyricsChanged(TrackLyricsChanged $event, StoredEvent $storedEvent): void
    {
        $this->recordModification(
            $event,
            $storedEvent,
            fn (TrackSnapshot $snapshot) => $snapshot->lyrics = $event->document
        );
    }

    public function onTrackAudioChanged(TrackAudioChanged $event, StoredEvent $storedEvent): void
    {
        $this->recordModification(
            $event,
            $storedEvent,
            fn (TrackSnapshot $snapshot) => $snapshot->audio = $event->path
        );
    }

    public function onTrackVideoChanged(TrackVideoChanged $event, StoredEvent $storedEvent): void
    {
        $this->recordModification(
            $event,
            $storedEvent,
            fn (TrackSnapshot $snapshot) => $snapshot->video = $event->url
        );
    }

    public function onTrackDeleted(TrackDeleted $event, StoredEvent $storedEvent): void
    {
        $last = $this->getLastRevision($event->id);

        $last->reviseForDeletion(
            $event->getUserId(),
            $storedEvent
        )->save();
    }

    public function resetState(): void
    {
        Revision::where('entity_type', EntityType::TRACK)->delete();
    }

    private function recordModification(TrackEvent $event, StoredEvent $storedEvent, callable $modify): void
    {
        $last = $this->getLastRevision($event->id);
        $snapshot = TrackSnapshot::fromRevision($last);

        $modify($snapshot);

        $last->revise(
            $snapshot,
            ChangeType::MODIFIED(),
            $event->getUserId(),
            $storedEvent
        )->save();
    }

    private function getLastRevision(string $id): Revision
    {
        $last = Revision::getLast(EntityType::TRACK(), $id);

        if ($last === null) {
            throw RevisionNotFoundException::forEntity(EntityType::TRACK, $id);
        }

        return $last;
    }
}
