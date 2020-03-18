<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Reciter;
use App\Queries\ReciterQuery;
use App\Support\Pagination\PaginationState;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ReciterRepository
{
    public function find(string $id): ?Reciter;

    /**
     * @return Collection|Reciter[]
     */
    public function all(string ...$ids): Collection;

    public function get(string $id): Reciter;

    public function paginate(PaginationState $state): LengthAwarePaginator;

    public function query(): ReciterQuery;

    public function persist(Reciter ...$reciters): void;
}
