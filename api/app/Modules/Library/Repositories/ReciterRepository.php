<?php

declare(strict_types=1);

namespace App\Modules\Library\Repositories;

use App\Modules\Library\Entities\Album as AlbumEntity;
use App\Modules\Library\Entities\Reciter as ReciterEntity;
use App\Modules\Library\Entities\Track as TrackEntity;
use App\Modules\Library\Models\Album as AlbumModel;
use App\Modules\Library\Models\Reciter as ReciterModel;
use App\Modules\Library\Models\Track as TrackModel;
use DB;
use Illuminate\Support\Collection;

class ReciterRepository
{
    private Collection $map;

    public function __construct()
    {
        $this->map = collect();
    }

    public function persist(ReciterEntity $reciter): void
    {
        $this->map->put($reciter->id, $reciter);
    }

    public function retrieve(string $id): ReciterEntity
    {
        if (!$this->map->has($id)) {
            $model = ReciterModel::query()
                ->with(['albums.tracks'])
                ->findOrFail($id);

            $this->map->put($id, ReciterEntity::fromModel($model));
        }

        /** @var ReciterEntity $reciter */
        $reciter = $this->map->get($id);

        return $reciter;
    }

    public function retrieveByAlbumId(string $albumId): ReciterEntity
    {
        $fromMemory = $this->map->first(fn (ReciterEntity $reciter) => $reciter->albums->has($albumId));

        if ($fromMemory) {
            return $fromMemory;
        }

        $albumModel = AlbumModel::retrieve($albumId);
        return $this->retrieve($albumModel->reciter_id);
    }

    public function remove(string $id): void
    {
        // TODO - handle deletes
    }

    public function flush(): void
    {
        DB::transaction(function () {
            $this->map->each(function (ReciterEntity $reciter) {
                $model = ReciterModel::findOrNew($reciter->id);

                $model->fill([
                    'name' => $reciter->name,
                    'description' => $reciter->description,
                    'avatar' => $reciter->avatar,
                ]);

                $model->save();

                $model->albums()->delete();

                $reciter->albums->each(function (AlbumEntity $album) use ($reciter) {
                    $model = AlbumModel::findOrNew($album->id);

                    $model->fill([
                        'title' => $album->title,
                        'year' => $album->year,
                        'artwork' => $album->artwork,
                        'reciter_id' => $reciter->id,
                    ]);

                    $model->save();

                    $model->tracks()->delete();

                    $album->tracks->each(function (TrackEntity $track) use ($album, $reciter) {
                        $model = TrackModel::findOrNew($track->id);

                        $model->fill([
                            'title' => $model->title,
                            'lyrics' => $model->lyrics,
                            'audio' => $model->audio,
                            'album_id' => $album->id,
                            'reciter_id' => $reciter->id,
                        ]);

                        $model->save();
                    });
                });
            });
        });
    }
}
