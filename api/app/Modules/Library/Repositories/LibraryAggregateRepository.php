<?php

declare(strict_types=1);

namespace App\Modules\Library\Repositories;

use App\Modules\Library\Data\Album as AlbumEntity;
use App\Modules\Library\Data\Reciter as ReciterEntity;
use App\Modules\Library\Data\Track as TrackEntity;
use App\Modules\Library\Models\Album as AlbumModel;
use App\Modules\Library\Models\Reciter as ReciterModel;
use App\Modules\Library\Models\Track as TrackModel;
use DB;
use Illuminate\Support\Collection;

class LibraryAggregateRepository
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
        $fromMemory = $this->map->first(
            fn (ReciterEntity $reciter) => $reciter->albums->has($albumId)
        );

        if ($fromMemory) {
            return $fromMemory;
        }

        $albumModel = AlbumModel::retrieve($albumId);
        return $this->retrieve($albumModel->reciter_id);
    }

    public function retrieveByTrackId(string $trackId): ReciterEntity
    {
        $fromMemory = $this->map->first(
            fn (ReciterEntity $r) => $r->albums->first(
                fn (AlbumEntity $a) => $a->tracks->has($trackId)
            )
        );

        if ($fromMemory) {
            return $fromMemory;
        }

        $trackModel = TrackModel::retrieve($trackId);

        return $this->retrieve($trackModel->reciter_id);
    }

    public function remove(string $id): void
    {
        $this->map->forget($id);

        /** @var ReciterModel|null $existing */
        $existing = ReciterModel::with('albums.tracks')->find($id);

        if ($existing) {
            $existing->deleteReciter();
        }
    }

    public function flush(): void
    {
        DB::transaction(function () {
            $this->map->each(function (ReciterEntity $reciter) {
                $this->flushReciter($reciter);
            });
        });
    }

    private function flushReciter(ReciterEntity $reciter): void
    {
        $reciterModel = ReciterModel::query()
            ->with('albums.tracks')
            ->findOrNew($reciter->id);

        $reciterModel->fill([
            'id' => $reciter->id,
            'name' => $reciter->name,
            'description' => $reciter->description,
            'avatar' => $reciter->avatar,
        ]);

        $reciterModel->save();

        $reciterModel->albums
            ->reject(fn (AlbumModel $a) => $reciter->albums->has($a->id))
            ->each(fn (AlbumModel $a) => $a->deleteAlbum());

        $reciter->albums->each(function (AlbumEntity $album) use ($reciter, $reciterModel) {
            /** @var AlbumModel|null $albumModel */
            $albumModel = $reciterModel->albums->first(fn (AlbumModel $a) => $a->id === $album->id);

            if (!$albumModel) {
                $albumModel = new AlbumModel(['id' => $album->id]);
            }

            $albumModel->fill([
                'title' => $album->title,
                'year' => $album->year,
                'artwork' => $album->artwork,
                'reciter_id' => $reciter->id,
            ]);

            $albumModel->save();

            $albumModel->tracks
                ->reject(fn (TrackModel $t) => $album->tracks->has($t->id))
                ->each(fn (TrackModel $t) => $t->delete());

            $album->tracks->each(function (TrackEntity $track) use ($album, $reciter, $albumModel) {
                /** @var TrackModel|null $trackModel */
                $trackModel = $albumModel->tracks->first(
                    fn (TrackModel $t) => $t->id === $track->id
                );

                if (!$trackModel) {
                    $trackModel = new TrackModel(['id' => $track->id]);
                }

                $trackModel->fill([
                    'title' => $track->title,
                    'lyrics' => $track->lyrics,
                    'audio' => $track->audio,
                    'album_id' => $album->id,
                    'reciter_id' => $reciter->id,
                ]);

                $trackModel->save();
            });
        });
    }
}
