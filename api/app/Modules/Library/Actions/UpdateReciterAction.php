<?php

declare(strict_types=1);

namespace App\Modules\Library\Actions;

use App\Entities\Reciter;
use App\Modules\Library\Events\ReciterModified;
use App\Repositories\ReciterRepository;
use Illuminate\Contracts\Events\Dispatcher;

class UpdateReciterAction
{
    private ReciterRepository $repository;
    private Dispatcher $dispatcher;

    public function __construct(ReciterRepository $repository, Dispatcher $dispatcher)
    {
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(Reciter $reciter, array $fields): void
    {
        // TODO - Want to handle the $fields variable in a better way.
        $request = collect($fields);

        if ($request->has('name')) {
            $reciter->setName($request->get('name'));
        }

        if ($request->has('description')) {
            $reciter->setDescription($request->get('description'));
        }

        $this->dispatcher->dispatch(new ReciterModified($reciter));
        $this->repository->persist($reciter);
    }
}
