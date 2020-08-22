<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Repositories\LibraryAggregateRepository;
use App\Modules\Library\Events\Reciters\{
    ReciterAvatarChanged,
    ReciterCreated,
    ReciterDeleted,
    ReciterDescriptionChanged,
    ReciterNameChanged
};
use App\Modules\Library\Models\Reciter as ReciterModel;
use App\Modules\Library\Entities\Reciter as ReciterEntity;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class RecitersProjector extends Projector
{
    private LibraryAggregateRepository $repository;

    public function __construct(LibraryAggregateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function onReciterCreated(ReciterCreated $event): void
    {
        $data = collect($event->attributes);

        $reciter = new ReciterEntity(
            $event->id,
            $data->get('name'),
            $data->get('description'),
            $data->get('avatar'),
        );

        $this->repository->persist($reciter);
    }

    public function onReciterNameChanged(ReciterNameChanged $event): void
    {
        $reciter = $this->repository->retrieve($event->id);
        $reciter->name = $event->name;
        $this->repository->persist($reciter);
    }

    public function onReciterDescriptionChanged(ReciterDescriptionChanged $event): void
    {
        $reciter = $this->repository->retrieve($event->id);
        $reciter->description = $event->description;
        $this->repository->persist($reciter);
    }

    public function onReciterAvatarChanged(ReciterAvatarChanged $event): void
    {
        $reciter = $this->repository->retrieve($event->id);
        $reciter->avatar = $event->avatar;
        $this->repository->persist($reciter);
    }

    public function onReciterDeleted(ReciterDeleted $event): void
    {
        $this->repository->remove($event->id);
    }

    public function resetState(): void
    {
        ReciterModel::truncate();
    }
}
