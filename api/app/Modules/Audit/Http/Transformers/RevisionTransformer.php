<?php

declare(strict_types=1);

namespace App\Modules\Audit\Http\Transformers;

use App\Modules\Audit\Models\Revision;
use App\Modules\Authentication\Http\Transformers\UserTransformer;
use App\Modules\Core\Transformers\Transformer;
use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Resource\Item;

class RevisionTransformer extends Transformer
{
    protected $availableIncludes = ['user'];

    protected $defaultIncludes = ['user'];

    public function toArray(Revision $revision): array
    {
        return [
            'id' => $revision->id,
            'type' => $revision->change_type,
            'entity' => $revision->entity_type,
            'entityId' => $revision->entity_id,
            'old' => $this->prepareSnapshot($revision->old_values, new EntityType($revision->getEntity())),
            'new' => $this->prepareSnapshot($revision->new_values, new EntityType($revision->getEntity())),
            'meta' => $this->getMeta($revision),
            'created_at' => $this->dateTime($revision->created_at),
        ];
    }

    public function includeUser(Revision $revision): Item
    {
        return $this->item($revision->user, new UserTransformer());
    }

    private function prepareSnapshot(?array $data, $type): ?array
    {
        if (empty($data)) {
            return null;
        }

        if ($type === Reciter::class) {
            $data['avatar'] = $this->qualifyAssetPath($data['avatar']);
        }

        if ($type === Album::class) {
            $data['artwork'] = $this->qualifyAssetPath($data['artwork']);
        }

        return $data;
    }

    private function getMeta(Revision $revision): array
    {
        switch ($revision->getEntity()) {
            case EntityType::RECITER:
                /** @var Reciter $reciter */
                $reciter = $this->em->repository(Reciter::class)->find($revision->getEntityId());
                return [
                    'link' => $reciter->getUrlPath(),
                ];
            case EntityType::ALBUM:
                /** @var Album $album */
                $album = $this->em->repository(Album::class)->find($revision->getEntityId());

                return [
                    'reciter' => $album->getReciter()->getName(),
                    'link' => $album->getUrlPath(),
                ];
            case EntityType::TRACK:
                /** @var Track $track */
                $track = $this->em->repository(Track::class)->find($revision->getEntityId());

                return [
                    'reciter' => $track->getReciter()->getName(),
                    'album' => $track->getAlbum()->getTitle(),
                    'year' => $track->getAlbum()->getYear(),
                    'link' => $track->getUrlPath(),
                ];
            case EntityType::LYRICS:
                /** @var Lyrics $lyrics */
                $lyrics = $this->em->repository(Lyrics::class)->find($revision->getEntityId());
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
