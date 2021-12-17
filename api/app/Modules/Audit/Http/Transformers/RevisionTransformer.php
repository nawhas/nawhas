<?php

declare(strict_types=1);

namespace App\Modules\Audit\Http\Transformers;

use App\Modules\Audit\Enum\EntityType;
use App\Modules\Audit\Models\Revision;
use App\Modules\Audit\Snapshots\AlbumSnapshot;
use App\Modules\Audit\Snapshots\ReciterSnapshot;
use App\Modules\Audit\Snapshots\TrackSnapshot;
use App\Modules\Authentication\Http\Transformers\UserTransformer;
use App\Modules\Core\Transformers\Transformer;
use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;
use App\Modules\Lyrics\Documents\Format;
use Illuminate\Support\Facades\Storage;
use App\Modules\Lyrics\Documents\Factory as DocumentFactory;
use App\Modules\Lyrics\Documents\JsonV1\Document as JsonDocument;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;
use League\Fractal\Resource\Primitive;

class RevisionTransformer extends Transformer
{
    protected $availableIncludes = ['user'];
    protected $defaultIncludes = ['user'];

    public function toArray(Revision $revision): array
    {
        return [
            'id' => $revision->id,
            'version' => $revision->version,
            'entityType' => $revision->entity_type,
            'entityId' => $revision->entity_id,
            'changeType' => $revision->change_type,
            'previous' => $this->prepareSnapshot($revision->old_values, $revision->entity_type),
            'snapshot' => $this->prepareSnapshot($revision->new_values, $revision->entity_type),
            'meta' => $this->getMeta($revision),
            'createdAt' => $this->dateTime($revision->created_at),
        ];
    }

    public function includeUser(Revision $revision): Primitive|Item|NullResource
    {
        if ($revision->user_id === null) {
            return $this->null();
        }

        return $this->item($revision->user, new UserTransformer());
    }

    private function prepareSnapshot(?array $data, EntityType $type): ?array
    {
        if (empty($data)) {
            return null;
        }

        if ($type === EntityType::Reciter) {
            if (isset($data['avatar'])) {
                $data['avatar'] = $this->qualifyAssetPath($data['avatar']);
            }
        }

        if ($type === EntityType::Album) {
            if (isset($data['artwork'])) {
                $data['artwork'] = $this->qualifyAssetPath($data['artwork']);
            }
        }

        if ($type === EntityType::Track) {
            if (isset($data['audio'])) {
                $data['audio'] = $this->qualifyAssetPath($data['audio']);
            }

            if (isset($data['lyrics'])) {
                $document = DocumentFactory::create(
                    $data['lyrics']['content'],
                    Format::from($data['lyrics']['format'])
                );
                $data['lyrics'] = $document->render();
                $data['timestamps'] = 'No';

                if ($document instanceof JsonDocument) {
                    $data['timestamps'] = $document->meta()->showTimestamps() ? 'Yes' : 'No';
                }
            }
        }

        return $data;
    }

    private function getMeta(Revision $revision): array
    {
        return match ($revision->entity_type) {
            EntityType::Reciter => [
                'link' => $revision->entity?->getUrlPath(),
            ],
            EntityType::Album => [
                'reciter' => $this->getReciterSnapshot($revision)->name,
                'link' => $revision->entity?->getUrlPath(),
            ],
            EntityType::Track => [
                'reciter' => $this->getReciterSnapshot($revision)->name,
                'album' => $this->getAlbumSnapshot($revision)->title,
                'year' => $this->getAlbumSnapshot($revision)->year,
                'link' => $revision->entity?->getUrlPath(),
            ]
        };
    }

    private function qualifyAssetPath(?string $path): ?string
    {
        return $path ? Storage::url($path) : null;
    }

    /**
     * Get the last snapshot of the reciter associated with the entity.
     */
    private function getReciterSnapshot(Revision $revision): ReciterSnapshot
    {
        if ($revision->entity_type === EntityType::Reciter) {
            return ReciterSnapshot::fromRevision($revision);
        }

        $id = (function (Revision $revision) {
            if ($revision->entity_type === EntityType::Album) {
                $album = AlbumSnapshot::fromRevision($revision);
                return $album->reciterId;
            }

            if ($revision->entity_type === EntityType::Track) {
                $track = TrackSnapshot::fromRevision($revision);
                $album = AlbumSnapshot::fromRevision(
                    Revision::getLast(EntityType::Album, $track->albumId)
                );
                return $album->reciterId;
            }

            throw new \BadMethodCallException('No reciter associated');
        })($revision);

        return ReciterSnapshot::fromRevision(
            Revision::getLast(EntityType::Reciter, $id)
        );
    }

    private function getAlbumSnapshot(Revision $revision): AlbumSnapshot
    {
        if ($revision->entity_type === EntityType::Album) {
            return AlbumSnapshot::fromRevision($revision);
        }

        if ($revision->entity_type === EntityType::Track) {
            $track = TrackSnapshot::fromRevision($revision);
            return AlbumSnapshot::fromRevision(
                Revision::getLast(EntityType::Album, $track->albumId)
            );
        }

        throw new \InvalidArgumentException('No album associated');
    }
}
