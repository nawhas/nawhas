<?php

declare(strict_types=1);

namespace App\Modules\Audit\Repositories;

use App\Modules\Audit\Entities\AuditRecord;
use App\Modules\Audit\Queries\AuditRecordQuery;
use App\Support\Pagination\PaginationState;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AuditRepository
{
    public function find(string $id): ?AuditRecord;
    public function all(string ...$ids): Collection;
    public function get(string $id): AuditRecord;
    public function query(): AuditRecordQuery;
    public function paginate(PaginationState $state): LengthAwarePaginator;
    public function persist(AuditRecord ...$auditRecord): void;
}
