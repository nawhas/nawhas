<?php

declare(strict_types=1);

namespace App\Modules\Audit\Transformers;

use App\Http\Transformers\Transformer;
use App\Http\Transformers\UserTransformer;
use App\Modules\Audit\Entities\AuditRecord;
use App\Modules\Audit\EntityType;
use App\Modules\Lyrics\Documents\Factory;
use App\Modules\Lyrics\Documents\Format;
use Illuminate\Support\Facades\Storage;
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
            'old' => $this->prepareSnapshot($audit->getOld(), new EntityType($audit->getEntity())),
            'new' => $this->prepareSnapshot($audit->getNew(), new EntityType($audit->getEntity())),
            $this->timestamps($audit),
        ];
    }

    public function includeUser(AuditRecord $audit): Item
    {
        return $this->item($audit->getUser(), new UserTransformer());
    }

    private function prepareSnapshot(?array $data, EntityType $type): ?array
    {
        if (empty($data)) {
            return null;
        }

        if ($type->getValue() === EntityType::RECITER) {
            $data['avatar'] = $this->qualifyAssetPath($data['avatar']);
        }

        if ($type->getValue() === EntityType::ALBUM) {
            $data['artwork'] = $this->qualifyAssetPath($data['artwork']);
        }

        if ($type->getValue() === EntityType::LYRICS) {
            if ($data['format'] !== null && $data['content'] !== null) {
                $data['content'] = Factory::create(
                    $data['content'],
                    new Format($data['format'])
                )->render();
            }
        }

        return $data;
    }

    private function qualifyAssetPath(?string $path): ?string
    {
        return $path ? Storage::url($path) : null;
    }
}
