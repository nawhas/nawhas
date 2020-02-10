<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Repositories;

use App\Exceptions\EntityNotFoundException;
use Doctrine\DBAL\Types\ConversionException;
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
        try {
            return $this->repo->find($id);
        } catch (ConversionException $e) {
            return null;
        }
    }

    public function findOrFail(string $id): Reciter
    {
        $reciter = $this->find($id);

        if ($reciter === null) {
            throw new EntityNotFoundException(Reciter::class, $id);
        }

        return $reciter;
    }

    public function paginateAll(PaginationState $state): LengthAwarePaginator
    {
        return $this->paginate($this->repo->createQueryBuilder('s')->getQuery(), $state);
    }
}
