<?php

declare(strict_types=1);

namespace App\Repositories\Doctrine;

use App\Database\Doctrine\EntityManager;
use App\Entities\Reciter;
use App\Entities\Track;
use App\Repositories\PopularEntitiesRepository;
use Illuminate\Support\Collection;

class DoctrinePopularEntitiesRepository implements PopularEntitiesRepository
{
    private const LIMIT = 20;

    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return Collection|Reciter[]
     */
    public function reciters(): Collection
    {
        $reciters = $this->em->repository(Reciter::class);

        $query = $reciters->createQueryBuilder('t')
            ->leftJoin('t.visits', 'v')
            ->addSelect('COUNT(v.id) as HIDDEN visits')
            ->groupBy('t.id')
            ->setMaxResults(self::LIMIT)
            ->orderBy('visits', 'desc')
            ->getQuery();

        return collect($query->getResult());
    }

    public function tracks(?Reciter $reciter = null): Collection
    {
        $tracks = $this->em->repository(Track::class);

        $builder = $tracks->createQueryBuilder('t')
            ->leftJoin('t.visits', 'v')
            ->leftJoin('t.album', 'a')
            ->addSelect('COUNT(v.id) as HIDDEN visits')
            ->groupBy('t.id, a.year')
            ->setMaxResults(self::LIMIT)
            ->addOrderBy('visits', 'desc')
            ->addOrderBy('a.year', 'desc');

        if ($reciter !== null) {
            $builder->andWhere('t.reciter = :reciter')->setParameter('reciter', $reciter);
        }

        $query = $builder->getQuery();

        return collect($query->getResult());
    }
}
