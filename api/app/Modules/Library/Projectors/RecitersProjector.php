<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\Reciters\{ReciterAvatarChanged,
    ReciterCreated,
    ReciterDeleted,
    ReciterDescriptionChanged,
    ReciterNameChanged};
use App\Modules\Library\Models\Reciter;
use Spatie\EventSourcing\Projectors\{Projector, ProjectsEvents};

class RecitersProjector implements Projector
{
    use ProjectsEvents;

    public function onReciterCreated(ReciterCreated $event): void
    {
        $data = collect($event->attributes);
        $data->put('id', $event->id);

        $reciter = new Reciter($data->all());

        $reciter->persist();
    }

    public function onReciterNameChanged(ReciterNameChanged $event): void
    {
        $reciter = Reciter::retrieve($event->id);
        $reciter->name = $event->name;
        $reciter->persist();
    }

    public function onReciterDescriptionChanged(ReciterDescriptionChanged $event): void
    {
        $reciter = Reciter::retrieve($event->id);
        $reciter->description = $event->description;
        $reciter->persist();
    }

    public function onReciterAvatarChanged(ReciterAvatarChanged $event): void
    {
        $reciter = Reciter::retrieve($event->id);
        $reciter->avatar = $event->avatar;
        $reciter->persist();
    }

    public function onReciterDeleted(ReciterDeleted $event): void
    {
        $reciter = Reciter::retrieve($event->id);
        $reciter->delete();
    }
}
