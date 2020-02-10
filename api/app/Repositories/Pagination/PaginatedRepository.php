<?php

declare(strict_types=1);

namespace App\Repositories\Pagination;

use Doctrine\ORM\AbstractQuery as Query;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaginatedRepository
{
    public function paginateAll(PaginationState $state): LengthAwarePaginator;
    public function paginate(Query $query, PaginationState $state): LengthAwarePaginator;
}
