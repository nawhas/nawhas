<?php

declare(strict_types=1);

namespace App\Modules\Audit\Projectors;

use App\Modules\Audit\Models\Revision;
use App\Modules\Audit\Models\Revisionable;
use App\Modules\Library\Events\Reciters\ReciterCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class RevisionsProjector extends Projector implements ShouldQueue
{
    public function onReciterCreated(StoredEvent $storedEvent, ReciterCreated $event): void
    {

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
