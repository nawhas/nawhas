<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\Reciters\ReciterViewed;
use App\Modules\Library\Events\Tracks\TrackViewed;
use App\Modules\Library\Models\Visit;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PopularEntitiesProjector extends Projector
{
    public function onReciterViewed(ReciterViewed $event): void
    {
        $data = collect($event->data);
        $data->put('id', $event->id);
        $data->put('entity', 'reciter');
        $data->put('entity_id', $data->get('reciter_id'));
        $data->forget('reciter_id');

        $visit = new Visit($data->all());

        $visit->saveOrFail();
    }

    public function onTrackViewed(TrackViewed $event): void
    {
        $data = collect($event->data);
        $data->put('id', $event->id);
        $data->put('entity', 'track');
        $data->put('entity_id', $data->get('track_id'));
        $data->forget('track_id');

        $visit = new Visit($data->all());

        $visit->saveOrFail();
    }
}
