<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Repositories;

use App\Database\Doctrine\Repositories\Support\{EntityRepository, PaginatesQueries};
use App\Entities\Reciter;
use App\Repositories\Pagination\PaginationState;
use App\Repositories\ReciterRepository as ReciterRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReciterRepository extends EntityRepository implements ReciterRepositoryContract
{
    use PaginatesQueries;

    public function find(string $id): ?Reciter
    {
        return $this->repo->find($id);
    }

    public function paginateAll(PaginationState $state): LengthAwarePaginator
    {
        return $this->paginate($this->repo->createQueryBuilder('s')->getQuery(), $state);
    }
}
