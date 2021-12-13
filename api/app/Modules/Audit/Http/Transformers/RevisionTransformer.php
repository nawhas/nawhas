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
use League\Fractal\Resource\Item;
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
            'previous' => $this->prepareSnapshot($revision->old_values, EntityType::from($revision->entity_type)),
            'snapshot' => $this->prepareSnapshot($revision->new_values, EntityType::from($revision->entity_type)),
            'meta' => $this->getMeta($revision),
            'createdAt' => $this->dateTime($revision->created_at),
        ];
    }

    public function includeUser(Revision $revision): Primitive|Item
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

        if ($type === EntityType::RECITER) {
            if (isset($data['avatar'])) {
                $data['avatar'] = $this->qualifyAssetPath($data['avatar']);
            }
        }

        if ($type === EntityType::ALBUM) {
            if (isset($data['artwork'])) {
                $data['artwork'] = $this->qualifyAssetPath($data['artwork']);
            }
        }

        if ($type === EntityType::TRACK) {
            if (isset($data['audio'])) {
                $data['audio'] = $this->qualifyAssetPath($data['audio']);
            }

            if (isset($data['lyrics'])) {
                $document = DocumentFactory::create(
                    $data['lyrics']['content'],
                    Format::tryFrom((int)$data['lyrics']['format'])
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
        switch ($revision->entity_type) {
            case EntityType::RECITER:
                /** @var Reciter|null $reciter */
                $reciter = $revision->entity;
                return [
                    'link' => optional($reciter)->getUrlPath(),
                ];
            case EntityType::ALBUM:
                /** @var Album|null $album */
                $album = $revision->entity;

                return [
                    'reciter' => $this->getReciterSnapshot($revision)->name,
                    'link' => optional($album)->getUrlPath(),
                ];
            case EntityType::TRACK:
                /** @var Track|null $track */
                $track = $revision->entity;
                $album = $this->getAlbumSnapshot($revision);

                return [
                    'reciter' => $this->getReciterSnapshot($revision)->name,
                    'album' => $album->title,
                    'year' => $album->year,
                    'link' => optional($track)->getUrlPath(),
                ];
            default:
                throw new \InvalidArgumentException('Unknown entity type.');
        }
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
        if ($revision->entity_type === EntityType::RECITER->value) {
            return ReciterSnapshot::fromRevision($revision);
        }

        $id = (function (Revision $revision) {
            if ($revision->entity_type === EntityType::ALBUM->value) {
                $album = AlbumSnapshot::fromRevision($revision);
                return $album->reciterId;
            }

            if ($revision->entity_type === EntityType::TRACK->value) {
                $track = TrackSnapshot::fromRevision($revision);
                $album = AlbumSnapshot::fromRevision(
                    Revision::getLast(EntityType::ALBUM, $track->albumId)
                );
                return $album->reciterId;
            }

            throw new \BadMethodCallException('No reciter associated');
        })($revision);

        return ReciterSnapshot::fromRevision(
            Revision::getLast(EntityType::RECITER, $id)
        );
    }

    private function getAlbumSnapshot(Revision $revision): AlbumSnapshot
    {
        if ($revision->entity_type === EntityType::ALBUM->value) {
            return AlbumSnapshot::fromRevision($revision);
        }

        if ($revision->entity_type === EntityType::TRACK->value) {
            $track = TrackSnapshot::fromRevision($revision);
            logger()->debug('Track Snapshot', ['track' => $track->toArray()]);
            return AlbumSnapshot::fromRevision(
                Revision::getLast(EntityType::ALBUM, $track->albumId)
            );
        }

        throw new \InvalidArgumentException('No album associated');
    }
}
