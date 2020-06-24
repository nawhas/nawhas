<?php

declare(strict_types=1);

namespace App\Modules\Audit\Events\Listeners;

use App\Modules\Audit\Auditor;
use App\Modules\Audit\Events\AuditableEvent;
use Doctrine\ORM\EntityManager;

class TrackEntityChanges
{
    private Auditor $auditor;

    public function __construct(Auditor $auditor)
    {
        $this->auditor = $auditor;
    }

    public function handle(AuditableEvent $event): void
    {
        $this->auditor->record($event->getEntity(), $event->get);
        logger()->debug('Found auditable event', [
            'event' => get_class($event),
            'entity_class' => get_class($event->getEntity()),
            'user' => $event->getUser()->getId(),
            'old' => $old,
            'entity' => $event->getEntity()->toArray(),
        ]);
    }
}
