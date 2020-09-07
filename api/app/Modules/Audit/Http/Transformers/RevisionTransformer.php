<?php

declare(strict_types=1);

namespace App\Modules\Audit\Http\Transformers;

use App\Modules\Audit\Enum\EntityType;
use App\Modules\Audit\Models\Revision;
use App\Modules\Audit\Snapshots\AlbumSnapshot;
use App\Modules\Audit\Snapshots\ReciterSnapshot;
use App\Modules\Authentication\Http\Transformers\UserTransformer;
use App\Modules\Core\Transformers\Transformer;
use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;
use App\Modules\Lyrics\Documents\Format;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Modules\Lyrics\Documents\Factory as DocumentFactory;
use App\Modules\Lyrics\Documents\JsonV1\Document as JsonDocument;
use League\Fractal\Resource\Item;

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
            'previous' => $this->prepareSnapshot($revision->old_values, new EntityType($revision->entity_type)),
            'snapshot' => $this->prepareSnapshot($revision->new_values, new EntityType($revision->entity_type)),
            'meta' => $this->getMeta($revision),
            'createdAt' => $this->dateTime($revision->created_at),
        ];
    }

    public function includeUser(Revision $revision)
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

        if ($type->equals(EntityType::RECITER())) {
            if (isset($data['avatar'])) {
                $data['avatar'] = $this->qualifyAssetPath($data['avatar']);
            }
        }

        if ($type->equals(EntityType::ALBUM())) {
            if (isset($data['artwork'])) {
                $data['artwork'] = $this->qualifyAssetPath($data['artwork']);
            }
        }

        if ($type->equals(EntityType::TRACK())) {
            if (isset($data['audio'])) {
                $data['audio'] = $this->qualifyAssetPath($data['audio']);
            }

            if (isset($data['lyrics'])) {
                $document = DocumentFactory::create(
                    $data['lyrics']['content'],
                    new Format($data['lyrics']['format'])
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
                /** @var Reciter $reciter */
                $reciter = $revision->entity;
                return [
                    'link' => $reciter->getUrlPath(),
                ];
            case EntityType::ALBUM:
                /** @var Album $album */
                $album = $revision->entity;

                return [
                    'reciter' => $this->getReciterName($album),
                    'link' => $album->getUrlPath(),
                ];
            case EntityType::TRACK:
                /** @var Track $track */
                $track = $revision->entity;

                return [
                    'reciter' => $this->getReciterName($track),
                    'album' => $this->getAlbum($track)->title,
                    'year' => $this->getAlbum($track)->year,
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

    /**
     * @param Album|Track $model
     * @return string
     */
    private function getReciterName($model): string
    {
        $reciter = $model->reciter;

        if ($reciter !== null) {
            return $reciter->name;
        }

        if ($model->reciter_id !== null) {
            $revision = Revision::getLast(Reciter::class, $model->reciter_id);
            return ReciterSnapshot::fromRevision($revision)->name;
        }

        throw new \InvalidArgumentException('No reciter associated');
    }

    /**
     * @return Album|AlbumSnapshot
     */
    private function getAlbum(Track $model)
    {
        $album = $model->album;

        if ($album !== null) {
            return $album;
        }

        if ($model->album_id !== null) {
            $revision = Revision::getLast(Album::class, $model->album_id);
            return AlbumSnapshot::fromRevision($revision);
        }

        throw new \InvalidArgumentException('No album associated');
    }
}
