<?php

declare(strict_types=1);

namespace App\Modules\Audit\Projectors;

use App\Modules\Audit\Events\RevisionableEvent;
use App\Modules\Audit\Jobs\WriteRevision;
use App\Modules\Audit\Models\Revision;
use App\Modules\Audit\Support\DiscoverRevisionableEvents;
use App\Modules\Library\Entities\Reciter;
use App\Modules\Library\Events\Reciters\ReciterCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\StoredEvents\StoredEvent;
use Illuminate\Support\Collection;

class RevisionsProjector extends Projector
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
        if ($event instanceof ReciterCreated) {
            $this->recordCreated($storedEvent, $event);
            return;
        }

        $this->recordRevision($storedEvent, $event);
    }

    private function recordCreated(StoredEvent $storedEvent, ReciterCreated $event): void
    {
        $data = collect($event->attributes);
        $reciter = new Reciter(
            $event->id,
            $data->get('name'),
            $data->get('description'),
            $data->get('avatar'),
        );

        $revision = new Revision();
        $revision->aggregate_id = $event->id;
        $revision->version = 1;
        $revision->old_values = [];
        $revision->new_values = $reciter->toSnapshot();
        $revision->user_id = $event->getUserId();
        $revision->change_type = $storedEvent->event_class;
        $revision->created_at = $storedEvent->created_at;

        $revision->save();
    }

    private function recordRevision(StoredEvent $storedEvent, RevisionableEvent $event): void
    {
        dispatch(new WriteRevision($storedEvent, $event))->onQueue('events');
    }

    public function resetState(): void
    {
        Revision::truncate();
    }
}
