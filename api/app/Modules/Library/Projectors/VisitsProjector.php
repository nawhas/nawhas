<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\Reciters\ReciterViewed;
use App\Modules\Library\Events\Tracks\TrackViewed;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;
use App\Modules\Library\Models\Visit;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class VisitsProjector extends Projector
{
    public function onReciterViewed(ReciterViewed $event): void
    {
        $data = collect($event->data);

        Visit::create([
            'date' => $data->get('date'),
            'visitable_id' => $event->id,
            'visitable_type' => Reciter::class,
        ]);
    }

    public function onTrackViewed(TrackViewed $event): void
    {
        $data = collect($event->data);

        Visit::create([
            'date' => $data->get('date'),
            'visitable_id' => $event->id,
            'visitable_type' => Track::class,
        ]);
    }
}
