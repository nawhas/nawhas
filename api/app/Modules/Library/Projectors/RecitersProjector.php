<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\ReciterCreated;
use App\Modules\Library\Models\Reciter;
use Spatie\EventSourcing\Projectors\{Projector, ProjectsEvents};
use Throwable;

class RecitersProjector implements Projector
{
    use ProjectsEvents;

    /**
     * @param ReciterCreated $event
     * @throws Throwable
     */
    public function onReciterCreated(ReciterCreated $event): void
    {
        $data = collect($event->attributes);
        $data->put('id', $event->id);

        $reciter = new Reciter($data->all());

        $reciter->saveOrFail();
    }
}
