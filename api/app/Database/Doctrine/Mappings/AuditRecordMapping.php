<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Mappings;

use App\Entities\User;
use App\Enum\ChangeType;
use App\Modules\Audit\Entities\AuditRecord;
use LaravelDoctrine\Fluent\{EntityMapping, Fluent};

class AuditRecordMapping extends EntityMapping
{
    public function mapFor()
    {
        return AuditRecord::class;
    }

    public function map(Fluent $map)
    {
        $map->uuidPrimaryKey();
        $map->field(ChangeType::class, 'type');
        $map->belongsTo(User::class, 'user');
        $map->string('entity');
        $map->string('entityId');
        $map->jsonArray('old')->nullable();
        $map->jsonArray('new')->nullable();
        $map->timestamps();
    }
}
