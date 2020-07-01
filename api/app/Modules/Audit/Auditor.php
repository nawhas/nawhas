<?php

declare(strict_types=1);

namespace App\Modules\Audit;

use App\Database\Doctrine\EntityManager;
use App\Entities\User;
use App\Enum\ChangeType;
use App\Modules\Audit\Entities\AuditableEntity;
use App\Modules\Audit\Entities\AuditRecord;
use App\Modules\Audit\Repositories\AuditRepository;
use Illuminate\Support\Collection;

class Auditor
{
    private EntityManager $em;
    private EntityResolver $resolver;
    private AuditRepository $repository;

    public function __construct(EntityManager $em, EntityResolver $resolver, AuditRepository $repository)
    {
        $this->em = $em;
        $this->resolver = $resolver;
        $this->repository = $repository;
    }

    public function record(AuditableEntity $entity, ChangeType $type, User $user): AuditRecord
    {
        $original = $type->equals(ChangeType::CREATED()) ? null : $this->getPreviousAttributes($entity)->toArray();
        $new = $type->equals(ChangeType::DELETED()) ? null : $this->getCurrentAttributes($entity)->toArray();
        $entityId = $entity->toArray()['id'];

        $audit = new AuditRecord($type, $user, $this->resolver->toLabel($entity), $entityId, $original, $new);
        $this->repository->persist($audit);

        return $audit;
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
