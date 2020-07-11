<?php

declare(strict_types=1);

namespace App\Queries;

use App\Entities\Contracts\Entity;
use App\Exceptions\EntityNotFoundException;
use App\Support\Pagination\PaginationState;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use Illuminate\Support\Collection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query as DoctrineQuery;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatorAdapter;

abstract class Query
{
    protected const QUERY_ALIAS = 't';
    protected QueryBuilder $builder;

    public function __construct(QueryBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return static
     */
    public static function make(): self
    {
        /** @var EntityManager $em */
        $em = app(EntityManager::class);

        /** @var EntityRepository $repo */
        $repo = $em->getRepository(static::entity());

        $queryBuilder = $repo->createQueryBuilder(static::QUERY_ALIAS);

        return new static($queryBuilder);
    }

    public function sortRandom(): self
    {
        // TODO - ???
        return $this;
    }

    public function first(): ?Entity
    {
        $query = $this->builder->getQuery();
        $query->setMaxResults(1);

        try {
            $result = $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }

        if (!is_array($result)) {
            return $result;
        }

        return array_shift($result);
    }

    public function get(): Entity
    {
        $entity = $this->first();

        if ($entity === null) {
            throw new EntityNotFoundException($this->entity());
        }

        return $entity;
    }

    public function count(): int
    {
        /** @var int $count */
        $count = $this->builder->select('count(' . static::QUERY_ALIAS . '.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return $count;
    }

    public function all(): Collection
    {
        return collect($this->builder->getQuery()->getResult());
    }

    public function build(): DoctrineQuery
    {
        return $this->builder->getQuery();
    }

    public function paginate(PaginationState $state): LengthAwarePaginator
    {
        return PaginatorAdapter::fromParams(
            $this->builder->getQuery(),
            $state->getLimit(),
            $state->getPage()
        )->make();
    }

    abstract protected static function entity(): string;
}
