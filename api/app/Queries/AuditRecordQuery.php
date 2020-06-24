<?php

declare(strict_types=1);

namespace App\Queries;

use App\Modules\Audit\Entities\AuditRecord;

class AuditRecordQuery extends Query
{
    public function whereIdentifier(string $identifier): self
    {
        $this->builder->andWhere('t.id = :id')
            ->setParameter(':id', $identifier);

        return $this;
    }

    public function whereEntityIdentifier(string $entityId): self
    {
        $this->builder->andWhere('t.entityId = :entityId')
            ->setParameter(':entityId', $entityId);
        
        return $this;
    }

    public function whereUser(string $userId): self
    {
        $this->builder->andWhere('t.user = :userId')
            ->setParameter(':userId', $userId);
        return $this;
    }

    protected static function entity(): string
    {
        return AuditRecord::class;
    }
}