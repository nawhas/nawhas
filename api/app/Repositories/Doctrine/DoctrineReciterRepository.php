<?php

declare(strict_types=1);

namespace App\Repositories\Doctrine;

use App\Entities\Reciter;
use App\Exceptions\EntityNotFoundException;
use App\Queries\ReciterQuery;
use App\Repositories\ReciterRepository;
use App\Support\Pagination\PaginationState;
use Illuminate\Support\Collection;
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

    /**
     * @return Collection|Reciter[]
     */
    public function popular(int $limit = 6): Collection
    {
        $query = $this->repo->createQueryBuilder('t')
            ->leftJoin('t.visits', 'v')
            ->addSelect('COUNT(v.id) as HIDDEN visits')
            ->groupBy('t.id')
            ->setMaxResults($limit)
            ->orderBy('visits', 'desc')
            ->getQuery();

        return collect($query->getResult());
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
