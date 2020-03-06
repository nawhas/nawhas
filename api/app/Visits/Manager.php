<?php

declare(strict_types=1);

namespace App\Visits;

use App\Database\Doctrine\EntityManager;

class Manager
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function record(Visitable ...$entities): void
    {
        foreach ($entities as $entity) {
            $this->em->persist($entity->visit());
        }
    }
}
