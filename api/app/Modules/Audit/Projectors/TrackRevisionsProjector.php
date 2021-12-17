<?php

declare(strict_types=1);

namespace App\Modules\Audit\Projectors;

use App\Modules\Core\Events\InteractsWithStoredEvents;
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
    use InteractsWithStoredEvents;

    public function onTrackCreated(TrackCreated $event): void
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
            ChangeType::Created,
            $event->getUserId(),
            $this->getStoredEvent($event)
        );

        $revision->save();
    }

    public function onTrackTitleChanged(TrackTitleChanged $event): void
    {
        $this->recordModification(
            $event,
            $this->getStoredEvent($event),
            fn (TrackSnapshot $snapshot) => $snapshot->title = $event->title
        );
    }

    public function onTrackLyricsChanged(TrackLyricsChanged $event): void
    {
        $this->recordModification(
            $event,
            $this->getStoredEvent($event),
            fn (TrackSnapshot $snapshot) => $snapshot->lyrics = $event->document
        );
    }

    public function onTrackAudioChanged(TrackAudioChanged $event): void
    {
        $this->recordModification(
            $event,
            $this->getStoredEvent($event),
            fn (TrackSnapshot $snapshot) => $snapshot->audio = $event->path
        );
    }

    public function onTrackVideoChanged(TrackVideoChanged $event): void
    {
        $this->recordModification(
            $event,
            $this->getStoredEvent($event),
            fn (TrackSnapshot $snapshot) => $snapshot->video = $event->url
        );
    }

    public function onTrackDeleted(TrackDeleted $event): void
    {
        $last = $this->getLastRevision($event->id);

        $last->reviseForDeletion(
            $event->getUserId(),
            $this->getStoredEvent($event)
        )->save();
    }

    public function resetState(): void
    {
        Revision::where('entity_type', EntityType::Track->value)->delete();
    }

    private function recordModification(TrackEvent $event, StoredEvent $storedEvent, callable $modify): void
    {
        $last = $this->getLastRevision($event->id);
        $snapshot = TrackSnapshot::fromRevision($last);

        $modify($snapshot);

        $last->revise(
            $snapshot,
            ChangeType::Modified,
            $event->getUserId(),
            $storedEvent
        )->save();
    }

    private function getLastRevision(string $id): Revision
    {
        $last = Revision::getLast(EntityType::Track, $id);

        if ($last === null) {
            throw RevisionNotFoundException::forEntity(EntityType::Track, $id);
        }

        return $last;
    }
}
