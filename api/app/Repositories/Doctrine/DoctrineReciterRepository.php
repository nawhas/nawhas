<?php

declare(strict_types=1);

namespace App\Repositories\Doctrine;

use App\Entities\Reciter;
use App\Exceptions\EntityNotFoundException;
use App\Queries\ReciterQuery;
use App\Repositories\ReciterRepository;
use App\Support\Pagination\PaginationState;
use Doctrine\Common\Collections\Criteria;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DoctrineReciterRepository extends DoctrineRepository implements ReciterRepository
{
    use PaginatesQueries;

    public function find(string $id): ?Reciter
    {
        return $this->query()->whereIdentifier($id)->first();
    }

    public function get(string $id): Reciter
    {
        $entity = $this->find($id);

        if (!$entity) {
            throw new EntityNotFoundException(Reciter::class, $id);
        }

        return $entity;
    }

    public function query(): ReciterQuery
    {
        return ReciterQuery::make();
    }

    public function paginate(PaginationState $state): LengthAwarePaginator
    {
        return $this->getPaginator($state);
    }

    public function persist(Reciter ...$reciters): void
    {
        $this->em->persist(...$reciters);
    }

    protected function entity(): string
    {
        return Reciter::class;
    }
}
