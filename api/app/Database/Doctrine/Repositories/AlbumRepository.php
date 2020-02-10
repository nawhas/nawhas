<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Repositories;

use App\Entities\Reciter;
use App\Exceptions\EntityNotFoundException;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\ConversionException;
use App\Database\Doctrine\Repositories\Support\{EntityRepository, PaginatesQueries};
use App\Entities\Album;
use App\Repositories\Pagination\PaginationState;
use App\Repositories\AlbumRepository as AlbumRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AlbumRepository extends EntityRepository implements AlbumRepositoryContract
{
    use PaginatesQueries;

    public function find(string $id): ?Album
    {
        try {
            return $this->repo->find($id);
        } catch (ConversionException $e) {
            return null;
        }
    }

    public function findOrFail(string $id): Album
    {
        $album = $this->find($id);

        if ($album === null) {
            throw new EntityNotFoundException(Album::class, $id);
        }

        return $album;
    }

    public function findForReciter(Reciter $reciter, string $id): ?Album
    {
        try {
            return $this->repo->findOneBy(['reciter' => $reciter->getId(), 'id' => $id]);
        } catch (ConversionException $e) {
            return null;
        }
    }

    public function findOrFailForReciter(Reciter $reciter, string $id): Album
    {
        $album = $this->findForReciter($reciter, $id);

        if (!$album) {
            throw new EntityNotFoundException(Album::class, $id);
        }

        return $album;
    }

    public function paginateAll(PaginationState $state): LengthAwarePaginator
    {
        return $this->paginate($this->repo->createQueryBuilder('s')->getQuery(), $state);
    }

    public function paginateForReciter(Reciter $reciter, PaginationState $state): LengthAwarePaginator
    {
        $query = $this->repo->createQueryBuilder('a')
            ->where('a.reciter = :reciter')
            ->setParameter('reciter', $reciter->getId())
            ->getQuery();

        return $this->paginate($query, $state);
    }
}
