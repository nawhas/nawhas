<?php

declare(strict_types=1);

namespace App\Repositories\Doctrine;

use App\Queries\Query;
use App\Support\Pagination\PaginationState;
use Doctrine\ORM\EntityRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatorAdapter;

trait PaginatesQueries
{
    protected function getPaginator(PaginationState $state, ?Query $query = null): LengthAwarePaginator
    {
        $doctrineQuery = $query ? $query->build() : $this->repo()->createQueryBuilder('p')->getQuery();

        return PaginatorAdapter::fromParams(
            $doctrineQuery,
            $state->getLimit(),
            $state->getPage()
        )->make();
    }

    abstract protected function repo(): EntityRepository;
}
