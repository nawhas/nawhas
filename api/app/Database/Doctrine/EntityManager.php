<?php

declare(strict_types=1);

namespace App\Database\Doctrine;

use App\Entities\Contracts\Entity;
use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;

class EntityManager
{
    private DoctrineEntityManager $em;

    public function __construct(DoctrineEntityManager $em)
    {
        $this->em = $em;
    }

    public function persist(Entity ...$entities): void
    {
        foreach ($entities as $entity) {
            $this->em->persist($entity);
        }
    }

    public function repository(string $entity): EntityRepository
    {
        /** @var EntityRepository $repo */
        $repo = $this->em->getRepository($entity);

        return $repo;
    }

    public function flush(): void
    {
        $this->em->flush();
    }
}
