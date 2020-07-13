<?php

declare(strict_types=1);

namespace App\Modules\Audit\Transformers;

use App\Http\Transformers\Transformer;
use App\Http\Transformers\UserTransformer;
use App\Modules\Audit\Entities\AuditRecord;
use League\Fractal\Resource\Item;

class AuditRecordTransformer extends Transformer
{
    protected $availableIncludes = ['user'];

    protected $defaultIncludes = ['user'];

    public function toArray(AuditRecord $audit): array
    {
        return [
            'id' => $audit->getId(),
            'type' => $audit->getType()->getValue(),
            'entity' => $audit->getEntity(),
            'entityId' => $audit->getEntityId(),
            'old' => $audit->getOld(),
            'new' => $audit->getNew(),
            $this->timestamps($audit),
        ];
    }

    public function includeUser(AuditRecord $audit): Item
    {
        return $this->item($audit->getUser(), new UserTransformer());
    }
}
