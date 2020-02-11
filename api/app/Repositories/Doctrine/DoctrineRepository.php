<?php declare(strict_types=1);

namespace App\Repositories\Doctrine;

use App\Database\Doctrine\EntityManager;
use App\Entities\Contracts\Entity;
use App\Exceptions\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;

abstract class DoctrineRepository
{
    protected EntityManager $em;
    protected ObjectRepository $repo;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repo = $this->em->repository($this->entity());
    }

    abstract protected function entity(): string;

    protected function findFromRepo(string $id): ?Entity
    {
        return $this->repo->find($id);
    }

    protected function getFromRepo(string $id): Entity
    {
        $entity = $this->findFromRepo($id);

        if (!$entity) {
            throw new EntityNotFoundException($this->entity(), $id);
        }

        return $entity;
    }

    protected function repo(): EntityRepository
    {
        return $this->repo;
    }
}
