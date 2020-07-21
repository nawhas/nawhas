<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\ReciterCreated;
use App\Modules\Library\Models\Reciter;
use Illuminate\Support\Str;
use Spatie\EventSourcing\Projectors\{Projector, ProjectsEvents};

class RecitersProjector implements Projector
{
    use ProjectsEvents;

    public function onReciterCreated(ReciterCreated $event): void
    {
        $reciter = new Reciter($event->attributes);
        $reciter->slug = Str::slug($reciter->name);
        $reciter->save();
    }
}
