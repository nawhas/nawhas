<?php

declare(strict_types=1);

namespace App\Repositories\Doctrine;

use App\Entities\Album;
use App\Entities\Reciter;
use App\Entities\Track;
use App\Queries\TrackQuery;
use App\Repositories\TrackRepository;
use Illuminate\Support\Collection;

/**
 * @method Track|null findFromRepo(string $id)
 * @method Track getFromRepo(string $id)
 */
class DoctrineTrackRepository extends DoctrineRepository implements TrackRepository
{
    public function find(string $id): ?Track
    {
        return $this->findFromRepo($id);
    }

    public function get(string $id): Track
    {
        return $this->getFromRepo($id);
    }

    public function getFromAlbum(Album $album, string $id): Track
    {
        return $this->query()
            ->whereAlbum($album)
            ->whereIdentifier($id)
            ->get();
    }

    public function allFromAlbum(Album $album): Collection
    {
        return $this->query()
            ->whereAlbum($album)
            ->all();
    }

    public function query(): TrackQuery
    {
        return TrackQuery::make();
    }

    protected function entity(): string
    {
        return Track::class;
    }

    /**
     * @return Collection|Track[]
     */
    public function popular(?Reciter $reciter = null, int $limit = 6): Collection
    {
        $builder = $this->repo->createQueryBuilder('t')
            ->leftJoin('t.visits', 'v')
            ->addSelect('COUNT(v.id) as HIDDEN visits')
            ->groupBy('t.id')
            ->setMaxResults($limit)
            ->orderBy('visits', 'desc');

        if ($reciter) {
            $builder->andWhere('t.reciter = :reciter')->setParameter('reciter', $reciter);
        }

        $query = $builder->getQuery();

        return collect($query->getResult());
    }

    public function persist(Track ...$tracks): void
    {
        $this->em->persist(...$tracks);
    }
}
