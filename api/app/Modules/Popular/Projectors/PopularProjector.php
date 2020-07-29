<?php

declare(strict_types=1);

namespace App\Modules\Popular\Projectors;

use App\Modules\Popular\Events\ModelVisited;
use App\Modules\Popular\Models\Visit;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PopularProjector extends Projector
{
    public function onModelVisited(ModelVisited $event): void
    {
        $data = collect($event->attributes);
        Visit::firstOrCreate([
            'ip' => $data->ip,
            'date' => $data->date,
            'visitable_id' => $data->visitableId,
            'visitable_type' => $data->visitableType,
        ]);
    }
}
