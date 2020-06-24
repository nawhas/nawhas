<?php

declare(strict_types=1);

namespace App\Modules\Audit;

use App\Database\Doctrine\EntityManager;
use App\Entities\User;
use App\Enum\ChangeType;
use App\Modules\Audit\Entities\AuditableEntity;
use Illuminate\Support\Collection;

class Auditor
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function record(
        AuditableEntity $entity,
        ChangeType $type,
        User $user
    ): void {
        $original = $type->equals(ChangeType::CREATED()) ? null : $this->getPreviousAttributes($entity);
        $new = $type->equals(ChangeType::DELETED()) ? null : $this->getCurrentAttributes($entity);

        // TODO - write audit record.
    }

    private function getPreviousAttributes(AuditableEntity $entity): Collection
    {
        $data = $this->em->getOriginalEntityData($entity);

        return collect($data)->only($entity->getTrackedFields());
    }

    private function getCurrentAttributes(AuditableEntity $entity): Collection
    {
        $data = $entity->toArray();

        return collect($data)->only($entity->getTrackedFields());
    }
}
