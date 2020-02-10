<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Reciter;
use App\Repositories\Pagination\PaginatedRepository;

interface ReciterRepository extends PaginatedRepository
{
    public function find(string $id): ?Reciter;
    public function findOrFail(string $id): Reciter;
}
