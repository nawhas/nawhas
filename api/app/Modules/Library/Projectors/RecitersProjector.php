<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\Reciters\{ReciterAvatarChanged,
    ReciterCreated,
    ReciterDeleted,
    ReciterDescriptionChanged,
    ReciterNameChanged};
use App\Modules\Library\Models\Reciter;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class RecitersProjector extends Projector
{
    public function onReciterCreated(ReciterCreated $event): void
    {
        $data = collect($event->attributes);
        $data->put('id', $event->id);

        $reciter = new Reciter($data->all());

        $reciter->saveOrFail();
    }

    public function onReciterNameChanged(ReciterNameChanged $event): void
    {
        $reciter = Reciter::retrieve($event->id);
        $reciter->name = $event->name;
        $reciter->saveOrFail();
    }

    public function onReciterDescriptionChanged(ReciterDescriptionChanged $event): void
    {
        $reciter = Reciter::retrieve($event->id);
        $reciter->description = $event->description;
        $reciter->saveOrFail();
    }

    public function onReciterAvatarChanged(ReciterAvatarChanged $event): void
    {
        $reciter = Reciter::retrieve($event->id);
        $reciter->avatar = $event->avatar;
        $reciter->saveOrFail();
    }

    public function onReciterDeleted(ReciterDeleted $event): void
    {
        $reciter = Reciter::retrieve($event->id);
        $reciter->delete();
    }

    public function resetState(): void
    {
        Reciter::truncate();
    }
}
