<?php

declare(strict_types=1);

namespace App\Modules\Audit\Repositories;

use App\Exceptions\EntityNotFoundException;
use App\Modules\Audit\Entities\AuditRecord;
use App\Modules\Audit\Queries\AuditRecordQuery;
use App\Repositories\Doctrine\DoctrineRepository;
use App\Repositories\Doctrine\PaginatesQueries;
use App\Support\Pagination\PaginationState;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DoctrineAuditRepository extends DoctrineRepository implements AuditRepository
{
    use PaginatesQueries;

    public function find(string $id): ?AuditRecord
    {
        return $this->query()->whereIdentifier($id)->first();
    }

    public function all(string ...$ids): Collection
    {
        $builder = $this->repo->createQueryBuilder('r');

        $result = $builder->where($builder->expr()->in('r.id', $ids))->getQuery()->getResult();

        return collect($result);
    }

    public function get(string $id): AuditRecord
    {
        $entity = $this->find($id);

        if (!$entity) {
            throw new EntityNotFoundException(AuditRecord::class, $id);
        }

        return $entity;
    }

    public function query(): AuditRecordQuery
    {
        return AuditRecordQuery::make();
    }

    public function persist(AuditRecord ...$auditRecords): void
    {
        $this->em->persist(...$auditRecords);
    }

    protected function entity(): string
    {
        return AuditRecord::class;
    }

    public function paginate(PaginationState $state): LengthAwarePaginator
    {
        $query = AuditRecordQuery::make()
            ->orderBy('createdAt', 'DESC');

        return $this->getPaginator($state, $query);
    }
}
