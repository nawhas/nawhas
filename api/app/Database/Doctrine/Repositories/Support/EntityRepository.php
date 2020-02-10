<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Repositories\Support;

use Doctrine\ORM\EntityRepository as DoctrineRepository;

abstract class EntityRepository
{
    protected DoctrineRepository $repo;

    public function __construct(DoctrineRepository $repo)
    {
        $this->repo = $repo;
    }
}
