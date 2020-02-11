<?php

declare(strict_types=1);

namespace App\Queries;

use App\Entities\Entity;
use App\Exceptions\EntityNotFoundException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

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
        $queryBuilder = $em->getRepository(static::entity())->createQueryBuilder(static::QUERY_ALIAS);
        return new static($queryBuilder);
    }

    public function first(): ?Entity
    {
        try {
            $result = $this->builder->getQuery()->getResult();
        } catch (NoResultException $e) {
            return null;
        }

        if (!is_array($result)) {
            return $result;
        }

        return array_shift($result);
    }

    public function firstOrFail(): Entity
    {
        $entity = $this->first();

        if ($entity === null) {
            throw new EntityNotFoundException($this->entity());
        }

        return $entity;
    }

    public function get(): Entity
    {
        $entity = $this->builder->getQuery()->getResult();

        if ($entity === null) {
            throw new EntityNotFoundException($this->entity());
        }

        return $entity;
    }

    abstract protected static function entity(): string;
}
