<?php

declare(strict_types=1);

namespace App\Modules\Library\Entities;

use App\Modules\Library\Models\Album as AlbumModel;
use App\Modules\Library\Models\Reciter as ReciterModel;
use App\Modules\Library\Models\Track as TrackModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class Reciter
{
    public string $id;
    public string $name;
    // public string $slug;
    public ?string $description;
    public ?string $avatar;

    public Collection $albums;

    public function __construct(
        string $id,
        string $name,
        ?string $description = null,
        ?string $avatar = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->avatar = $avatar;
        $this->albums = collect();
    }

    public static function fromModel(ReciterModel $model): self
    {
        $reciter = new self(
            $model->id,
            $model->name,
            $model->description,
            $model->avatar,
        );

        $model->albums->each(function (AlbumModel $albumModel) use ($reciter) {
            $album = new Album(
                $albumModel->id,
                $albumModel->title,
                $albumModel->year,
                $albumModel->artwork,
            );

            $reciter->addAlbum($album);

            $albumModel->tracks->mapWithKeys(function (TrackModel $trackModel) {
                $track = new Track();
                $track->id = $trackModel->id;
                $track->title = $trackModel->title;
                $track->lyrics = $trackModel->lyrics;
                $track->audio = $trackModel->audio;

                return [
                    $track->id => $track,
                ];
            });

            return [
                $album->id => $album,
            ];
        });

        return $reciter;
    }

    public function addAlbum(Album $album): self
    {
        $this->albums->put($album->id, $album);

        return $this;
    }

    public function getAlbum(string $id): Album
    {
        /** @var Album $album */
        $album = $this->albums->get($id);

        if (!$album) {
            throw new ModelNotFoundException("Album#{$id} not found.");
        }

        return $album;
    }

    public function removeAlbum(string $id): self
    {
        $this->albums->forget($id);
    }
}
