<?php

declare(strict_types=1);

namespace App\Repositories\Doctrine;

use App\Entities\{Album, Reciter};
use App\Queries\AlbumQuery;
use App\Repositories\AlbumRepository;
use App\Support\Pagination\PaginationState;
use Doctrine\Common\Collections\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DoctrineAlbumRepository extends DoctrineRepository implements AlbumRepository
{
    use PaginatesQueries;

    public function find(string $id): ?Album
    {
        return $this->findFromRepo($id);
    }

    public function get(string $id): Album
    {
        return $this->getFromRepo($id);
    }

    public function getByReciter(Reciter $reciter, string $id): Album
    {
        return $this->query()
            ->whereReciter($reciter)
            ->whereIdentifier($id)
            ->get();
    }

    public function allByReciter(Reciter $reciter): Collection
    {
        return $this->query()
            ->whereReciter($reciter)
            ->all();
    }

    public function query(): AlbumQuery
    {
        return AlbumQuery::make();
    }

    protected function entity(): string
    {
        return Album::class;
    }

    public function paginateAllByReciter(Reciter $reciter, PaginationState $state): LengthAwarePaginator
    {
        return $this->query()->whereReciter($reciter)->paginate($state);
    }
}
