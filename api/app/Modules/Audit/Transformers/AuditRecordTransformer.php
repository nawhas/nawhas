<?php

declare(strict_types=1);

namespace App\Modules\Audit\Transformers;

use App\Database\Doctrine\EntityManager;
use App\Entities\Album;
use App\Entities\Lyrics;
use App\Entities\Reciter;
use App\Entities\Track;
use App\Http\Transformers\Transformer;
use App\Http\Transformers\UserTransformer;
use App\Modules\Audit\Entities\AuditRecord;
use App\Modules\Audit\EntityType;
use App\Modules\Lyrics\Documents\Factory as DocumentFactory;
use App\Modules\Lyrics\Documents\Format;
use App\Modules\Lyrics\Documents\JsonV1\Document as JsonDocument;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Resource\Item;

class AuditRecordTransformer extends Transformer
{
    protected $availableIncludes = ['user'];

    protected $defaultIncludes = ['user'];

    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function toArray(AuditRecord $audit): array
    {
        return [
            'id' => $audit->getId(),
            'type' => $audit->getType()->getValue(),
            'entity' => $audit->getEntity(),
            'entityId' => $audit->getEntityId(),
            'old' => $this->prepareSnapshot($audit->getOld(), new EntityType($audit->getEntity())),
            'new' => $this->prepareSnapshot($audit->getNew(), new EntityType($audit->getEntity())),
            'meta' => $this->getMeta($audit),
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
                $document = DocumentFactory::create(
                    $data['content'],
                    new Format($data['format'])
                );
                $data['content'] = $document->render();
                $data['timestamps'] = null;

                if ($document instanceof JsonDocument) {
                    $data['timestamps'] = $document->meta()->showTimestamps() ? 'Yes' : 'No';
                }
            }
        }

        return $data;
    }

    private function getMeta(AuditRecord $audit): array
    {
        switch ($audit->getEntity()) {
            case EntityType::RECITER:
                /** @var Reciter $reciter */
                $reciter = $this->em->repository(Reciter::class)->find($audit->getEntityId());
                return [
                    'link' => $reciter->getUrlPath(),
                ];
            case EntityType::ALBUM:
                /** @var Album $album */
                $album = $this->em->repository(Album::class)->find($audit->getEntityId());

                return [
                    'reciter' => $album->getReciter()->getName(),
                    'link' => $album->getUrlPath(),
                ];
            case EntityType::TRACK:
                /** @var Track $track */
                $track = $this->em->repository(Track::class)->find($audit->getEntityId());

                return [
                    'reciter' => $track->getReciter()->getName(),
                    'album' => $track->getAlbum()->getTitle(),
                    'year' => $track->getAlbum()->getYear(),
                    'link' => $track->getUrlPath(),
                ];
            case EntityType::LYRICS:
                /** @var Lyrics $lyrics */
                $lyrics = $this->em->repository(Lyrics::class)->find($audit->getEntityId());
                $track = $lyrics->getTrack();

                return [
                    'title' => $track->getTitle(),
                    'reciter' => $track->getReciter()->getName(),
                    'album' => $track->getAlbum()->getTitle(),
                    'year' => $track->getAlbum()->getYear(),
                    'link' => $track->getUrlPath(),
                ];
            default:
                throw new \InvalidArgumentException('Unknown entity type.');
        }
    }

    private function qualifyAssetPath(?string $path): ?string
    {
        return $path ? Storage::url($path) : null;
    }
}
