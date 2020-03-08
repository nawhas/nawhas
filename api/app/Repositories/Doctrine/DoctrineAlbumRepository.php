<?php

declare(strict_types=1);

namespace App\Repositories\Doctrine;

use App\Entities\{Album, Contracts\Entity, Reciter};
use App\Queries\AlbumQuery;
use App\Repositories\AlbumRepository;
use Illuminate\Support\Collection;
use App\Support\Pagination\PaginationState;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @method Album|null findFromRepo(string $id)
 * @method Album getFromRepo(string $id)
 */
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

    public function persist(Album ...$albums): void
    {
        $this->em->persist(...$albums);
    }
}
