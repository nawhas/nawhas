<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Repositories\Support;

use App\Repositories\Pagination\PaginationState;
use Doctrine\ORM\AbstractQuery as Query;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatorAdapter;

trait PaginatesQueries
{
    public function paginate(Query $query, PaginationState $state): LengthAwarePaginator
    {
        return PaginatorAdapter::fromParams($query, $state->getLimit(), $state->getPage())->make();
    }
}
