<?php

namespace App\Modules\Audit\Jobs;

use App\Modules\Audit\Events\RevisionableEvent;
use App\Modules\Audit\Models\Revision;
use App\Modules\Library\Data\Reciter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class WriteRevision implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected StoredEvent $storedEvent;
    protected RevisionableEvent $event;

    public function __construct(
        StoredEvent $storedEvent,
        RevisionableEvent $event
    ) {
        $this->storedEvent = $storedEvent;
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $last = $this->getLastRevision($this->storedEvent->aggregate_uuid);

        if (!$last) {
            throw new ModelNotFoundException(
                "No initial revision found for aggregate {$this->storedEvent->aggregate_uuid}"
            );
        }

        $reciter = Reciter::fromArray($last->new_values);

        $revision = new Revision();
        $revision->aggregate_id = $this->storedEvent->aggregate_uuid;
        $revision->change_type = $this->storedEvent->event_class;
        $revision->user_id = $this->event->getUserId();
        $revision->created_at = $this->storedEvent->created_at;
        $revision->version = $last->version + 1;
        $revision->old_values = $last->new_values;
        $revision->new_values = $reciter->apply($this->event)->toSnapshot();
        $revision->save();
    }

    private function getLastRevision(string $aggregateId): ?Revision
    {
        return Revision::query()
            ->where('aggregate_id', $aggregateId)
            ->orderByDesc('version')
            ->first(['new_values', 'version']);
    }
}
