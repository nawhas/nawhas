<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Album;
use App\Entities\Reciter;
use App\Repositories\Pagination\PaginatedRepository;
use App\Repositories\Pagination\PaginationState;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AlbumRepository extends PaginatedRepository
{
    public function find(string $id): ?Album;
    public function findOrFail(string $id): Album;
    public function paginateForReciter(Reciter $reciter, PaginationState $state): LengthAwarePaginator;
    public function findForReciter(Reciter $reciter, string $id): ?Album;
    public function findOrFailForReciter(Reciter $reciter, string $id): Album;
}
