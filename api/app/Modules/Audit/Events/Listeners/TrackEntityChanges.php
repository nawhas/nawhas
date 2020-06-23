<?php

declare(strict_types=1);

namespace App\Modules\Audit\Events\Listeners;

use App\Modules\Audit\Events\AuditableEvent;

class TrackEntityChanges
{
    public function handle(AuditableEvent $event): void
    {
        logger()->debug('Handling ' . get_class($event) . ' event');
    }
}
