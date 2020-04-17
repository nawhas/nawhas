<?php

declare(strict_types=1);

namespace App\Repositories\Doctrine;

use App\Database\Doctrine\EntityManager;
use App\Entities\Reciter;
use App\Entities\Track;
use App\Repositories\PopularEntitiesRepository;
use App\Support\Pagination\PaginationState;
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
    public function reciters(PaginationState $pagination): Collection
    {
        $reciters = $this->em->repository(Reciter::class);

        $query = $reciters->createQueryBuilder('t')
            ->leftJoin('t.visits', 'v')
            ->addSelect('COUNT(v.id) as HIDDEN visits')
            ->groupBy('t.id')
            ->setMaxResults($pagination->getLimit(self::LIMIT))
            ->orderBy('visits', 'desc')
            ->getQuery();

        return collect($query->getResult());
    }

    public function tracks(PaginationState $pagination, ?Reciter $reciter = null): Collection
    {
        $tracks = $this->em->repository(Track::class);

        $builder = $tracks->createQueryBuilder('t')
            ->leftJoin('t.visits', 'v')
            ->leftJoin('t.album', 'a')
            ->addSelect('COUNT(v.id) as HIDDEN visits')
            ->groupBy('t.id, a.year')
            ->setMaxResults($pagination->getLimit(self::LIMIT))
            ->addOrderBy('visits', 'desc')
            ->addOrderBy('a.year', 'desc');

        if ($reciter !== null) {
            $builder->andWhere('t.reciter = :reciter')->setParameter('reciter', $reciter);
        }

        $query = $builder->getQuery();

        return collect($query->getResult());
    }
}
