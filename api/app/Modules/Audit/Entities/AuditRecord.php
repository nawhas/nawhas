<?php

declare(strict_types=1);

namespace App\Modules\Audit\Entities;

class AuditRecord
{
    private ?array $old;
    private ?array $new;
}
