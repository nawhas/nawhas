<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\Topics\TopicCreated;
use App\Modules\Library\Models\Topic;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class TopicsProjector extends Projector
{
    public function onTopicCreated(TopicCreated $event): void
    {
        $data = collect($event->attributes);

        $topic = new Topic($data->all());
        $topic->id = $event->id;
        $topic->save();
    }

    public function resetState(): void
    {
        Topic::truncate();
    }
}
