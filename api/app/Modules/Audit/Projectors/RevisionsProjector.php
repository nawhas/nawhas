<?php

declare(strict_types=1);

namespace App\Modules\Audit\Projectors;

use App\Modules\Audit\Events\RevisionableEvent;
use App\Modules\Audit\Models\Revision;
use App\Modules\Audit\Models\Revisionable;
use App\Modules\Audit\Support\DiscoverRevisionableEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\StoredEvents\StoredEvent;
use Illuminate\Support\Collection;

class RevisionsProjector extends Projector implements ShouldQueue
{
    public function getEventHandlingMethods(): Collection
    {
        return collect($this->handles())->mapWithKeys(fn ($event) => [$event => 'onRevisionableEvent']);
    }

    public function handles(): array
    {
        return (new DiscoverRevisionableEvents())->discover();
    }

    public function onRevisionableEvent(StoredEvent $storedEvent, RevisionableEvent $event): void
    {
        $this->recordRevision($storedEvent, $event);
    }

    private function recordRevision(StoredEvent $storedEvent, RevisionableEvent $event): void
    {
        $model = $event->revisionable();
        $last = $this->getLastRevision($model);

        $revision = new Revision();
        $revision->entity_type = get_class($model);
        $revision->entity_id = $model->getKey();
        $revision->change_type = $event->changeType()->getValue();
        $revision->user_id = $event->getUserId();
        $revision->created_at = $storedEvent->created_at;
        $revision->version = $last ? $last->version + 1 : 1;

        $revision->old_values = $last ? $last->new_values : [];
        $revision->new_values = $model->getRevisionableAttributes();

        if (!$revision->hasDiff()) {
            return;
        }

        $revision->save();
    }

    private function getLastRevision(Revisionable $revisionable): ?Revision
    {
        return $revisionable->revisions()->latest()->first();
    }

    public function resetState(): void
    {
        Revision::truncate();
    }
}
