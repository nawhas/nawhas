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

    public function record(AuditableEvent $event): AuditRecord
    {
        $entity = $event->getEntity();

        $audit = new AuditRecord(
            $event->getChangeType(),
            $this->guard->user(),
            $this->resolver->toLabel($entity),
            $entity->getId(),
            $this->getPreviousAttributes($event),
            $this->getCurrentAttributes($event)
        );

        $this->repository->persist($audit);

        return $audit;
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
