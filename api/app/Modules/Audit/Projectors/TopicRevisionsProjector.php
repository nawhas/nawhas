<?php

declare(strict_types=1);

namespace App\Modules\Audit\Projectors;

use App\Modules\Library\Events\Topics\TopicEvent;
use App\Modules\Audit\Enum\{ChangeType, EntityType};
use App\Modules\Audit\Exceptions\RevisionNotFoundException;
use App\Modules\Audit\Models\Revision;
use App\Modules\Audit\Snapshots\TopicSnapshot;
use App\Modules\Library\Events\Topics\TopicCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class TopicRevisionsProjector extends Projector
{
    public function onTopicCreated(TopicCreated $event, StoredEvent $storedEvent): void
    {
        $data = collect($event->attributes);
        $snapshot = new TopicSnapshot(
            $event->id,
            $data->get('name'),
            $data->get('description'),
            $data->get('image'),
        );

        $revision = Revision::makeInitial(
            $snapshot,
            ChangeType::CREATED(),
            $event->getUserId(),
            $storedEvent,
        );

        $revision->save();
    }

    public function resetState(): void
    {
        Revision::where('entity_type', EntityType::TOPIC)->delete();
    }

    private function getLastRevision(string $id): Revision
    {
        $last = Revision::getLast(EntityType::TOPIC(), $id);

        if ($last === null) {
            throw RevisionNotFoundException::forEntity(EntityType::TOPIC, $id);
        }

        return $last;
    }

    private function recordModification(TopicEvent $event, StoredEvent $storedEvent, callable $modify): void
    {
        $last = $this->getLastRevision($event->id);
        $snapshot = TopicSnapshot::fromRevision($last);

        $modify($snapshot);

        $last->revise(
            $snapshot,
            ChangeType::MODIFIED(),
            $event->getUserId(),
            $storedEvent
        )->save();
    }
}
