<?php

declare(strict_types=1);

namespace App\Modules\Library\Actions;

use App\Entities\Reciter;
use App\Modules\Library\Events\ReciterCreated;
use App\Repositories\ReciterRepository;
use Illuminate\Contracts\Events\Dispatcher;

class CreateReciterAction
{
    private ReciterRepository $repository;
    private Dispatcher $dispatcher;

    public function __construct(ReciterRepository $repository, Dispatcher $dispatcher)
    {
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $name, ?string $description = null): Reciter
    {
        $reciter = new Reciter($name, $description);
        $this->repository->persist($reciter);

        $this->dispatcher->dispatch(new ReciterCreated($reciter));

        return $reciter;
    }
}
