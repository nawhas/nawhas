<?php

declare(strict_types=1);

namespace App\Modules\Library\Actions;

use App\Entities\Reciter;
use App\Modules\Library\Events\ReciterModified;
use App\Repositories\ReciterRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\UploadedFile;

class SetReciterAvatarAction
{
    private ReciterRepository $repository;
    private Dispatcher $dispatcher;

    public function __construct(ReciterRepository $repository, Dispatcher $dispatcher)
    {
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(Reciter $reciter, UploadedFile $avatar): void
    {
        $path = $avatar->storePublicly("reciters/{$reciter->getSlug()}");

        $reciter->setAvatar($path);

        $this->dispatcher->dispatch(new ReciterModified($reciter));
        $this->repository->persist($reciter);
    }
}
