<?php

declare(strict_types=1);

namespace App\Modules\Audit\Events\Listeners;

use App\Modules\Audit\Auditor;
use App\Modules\Audit\Events\AuditableEvent;

class TrackEntityChanges
{
    private Auditor $auditor;

    public function __construct(Auditor $auditor)
    {
        $this->auditor = $auditor;
    }

    public function handle(AuditableEvent $event): void
    {
        logger()->debug('TrackEntityChanges :: received ' . get_class($event));
        $this->auditor->record($event);
    }
}
