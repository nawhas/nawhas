<?php

declare(strict_types=1);

namespace App\Modules\Audit;

use App\Database\Doctrine\EntityManager;
use App\Entities\User;
use App\Enum\ChangeType;
use App\Modules\Audit\Entities\AuditableEntity;
use App\Modules\Audit\Entities\AuditRecord;
use Illuminate\Support\Collection;

class Auditor
{
    private EntityManager $em;
    private EntityResolver $resolver;

    public function __construct(EntityManager $em, EntityResolver $resolver)
    {
        $this->em = $em;
        $this->resolver = $resolver;
    }

    public function record(AuditableEntity $entity, ChangeType $type, User $user): void
    {
        $original = $type->equals(ChangeType::CREATED()) ? null : $this->getPreviousAttributes($entity)->toArray();
        $new = $type->equals(ChangeType::DELETED()) ? null : $this->getCurrentAttributes($entity)->toArray();
        $entityId = $entity->toArray()['id'];

        // TODO - write audit record.
        $audit = new AuditRecord($type, $user, $this->resolver->toLabel($entity), $entityId, $original, $new);
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
