<?php

declare(strict_types=1);

namespace App\Modules\Audit;

use App\Database\Doctrine\EntityManager;
use App\Enum\ChangeType;
use App\Modules\Audit\Entities\AuditRecord;
use App\Modules\Audit\Events\AuditableEvent;
use App\Modules\Audit\Events\ChangeAwareAuditableEvent;
use App\Modules\Audit\Repositories\AuditRepository;
use App\Modules\Authentication\Guard;

class Auditor
{
    private EntityManager $em;
    private EntityResolver $resolver;
    private AuditRepository $repository;
    private Guard $guard;

    public function __construct(
        EntityManager $em,
        EntityResolver $resolver,
        AuditRepository $repository,
        Guard $guard
    ) {
        $this->em = $em;
        $this->resolver = $resolver;
        $this->repository = $repository;
        $this->guard = $guard;
    }

    public function record(AuditableEvent $event): ?AuditRecord
    {
        $name = get_class($event);
        $entity = $event->getEntity();

        $old = $this->getPreviousAttributes($event);
        $new = $this->getCurrentAttributes($event);

        if ($event->getChangeType() === ChangeType::UPDATED() && !$this->hasChanges($old, $new)) {
            logger()->debug('[Auditor] No changes found in event: ' . $name);
            return null;
        }

        $audit = new AuditRecord(
            $event->getChangeType(),
            $this->guard->user(),
            $this->resolver->toLabel($entity),
            $entity->getId(),
            $old,
            $new,
        );

        $this->repository->persist($audit);
        logger()->debug('[Auditor] Wrote audit record for event: ' . $name);

        return $audit;
    }

    private function hasChanges(array $old, array $new): bool
    {
        return collect($new)
            ->filter(fn ($value, $key) => $value !== $old[$key])
            ->isNotEmpty();
    }

    private function getPreviousAttributes(AuditableEvent $event): ?array
    {
        if ($event->getChangeType()->equals(ChangeType::CREATED())) {
            return null;
        }

        if ($event instanceof ChangeAwareAuditableEvent) {
            $data = $event->getPreviousEntity()->toArray();
        } else {
            $data = $this->em->getOriginalEntityData($event->getEntity());
        }

        return collect($data)->only($event->getEntity()->getTrackedFields())->toArray();
    }

    private function getCurrentAttributes(AuditableEvent $event): ?array
    {
        if ($event->getChangeType()->equals(ChangeType::DELETED())) {
            return null;
        }

        $data = $event->getEntity()->toArray();

        return collect($data)->only($event->getEntity()->getTrackedFields())->toArray();
    }
}
