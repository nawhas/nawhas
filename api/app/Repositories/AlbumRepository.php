<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\{Album, Reciter};
use App\Queries\AlbumQuery;
use App\Support\Pagination\PaginationState;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AlbumRepository
{
    public function find(string $id): ?Album;
    public function get(string $id): Album;
    public function getByReciter(Reciter $reciter, string $id): Album;
    public function allByReciter(Reciter $reciter): Collection;
    public function paginateAllByReciter(Reciter $reciter, PaginationState $state): LengthAwarePaginator;
    public function query(): AlbumQuery;
    public function persist(Album ...$albums): void;
}
