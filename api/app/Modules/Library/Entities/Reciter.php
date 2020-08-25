<?php

declare(strict_types=1);

namespace App\Modules\Library\Entities;

use App\Modules\Library\Entities\Album as AlbumEntity;
use App\Modules\Library\Entities\Track as TrackEntity;
use App\Modules\Library\Events\Albums\AlbumArtworkChanged;
use App\Modules\Library\Events\Albums\AlbumCreated;
use App\Modules\Library\Events\Albums\AlbumDeleted;
use App\Modules\Library\Events\Albums\AlbumTitleChanged;
use App\Modules\Library\Events\Albums\AlbumYearChanged;
use App\Modules\Library\Events\Reciters\ReciterAvatarChanged;
use App\Modules\Library\Events\Reciters\ReciterDescriptionChanged;
use App\Modules\Library\Events\Reciters\ReciterNameChanged;
use App\Modules\Library\Events\Tracks\TrackAudioChanged;
use App\Modules\Library\Events\Tracks\TrackCreated;
use App\Modules\Library\Events\Tracks\TrackDeleted;
use App\Modules\Library\Events\Tracks\TrackLyricsChanged;
use App\Modules\Library\Events\Tracks\TrackTitleChanged;
use App\Modules\Library\Models\Album as AlbumModel;
use App\Modules\Library\Models\Reciter as ReciterModel;
use App\Modules\Library\Models\Track as TrackModel;
use App\Modules\Lyrics\Documents\Factory;
use App\Modules\Lyrics\Documents\Format;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use ReflectionClass;

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

    public static function fromArray(array $attributes): self
    {
        $reciter = new self(
            $attributes['id'],
            $attributes['name'],
            $attributes['description'],
            $attributes['avatar'],
        );

        collect($attributes['albums'])->each(function (array $attributes) use ($reciter) {
            $album = new Album(
                $attributes['id'],
                $attributes['title'],
                $attributes['year'],
                $attributes['artwork'],
            );

            $reciter->addAlbum($album);

            collect($attributes['tracks'])->each(function (array $attributes) use ($album) {
                $lyrics = $attributes['lyrics'];

                if ($lyrics) {
                    $lyrics = Factory::create($lyrics['content'], new Format($lyrics['format']));
                }

                $track = new Track(
                    $attributes['id'],
                    $attributes['title'],
                    $lyrics,
                    $attributes['audio'],
                );

                $album->addTrack($track);
            });
        });

        return $reciter;
    }

    public static function fromModel(ReciterModel $model): self
    {
        return self::fromArray($model->toArray());
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

    public function getAlbumForTrack(string $id, ?string $albumId = null): Album
    {
        if ($albumId !== null) {
            return $this->getAlbum($albumId);
        }

        /** @var Album|null $album */
        $album = $this->albums->first(fn (Album $a) => $a->tracks->has($id));

        if (!$album) {
            throw new ModelNotFoundException("Track#{$id} not found.");
        }

        return $album;
    }

    public function getTrack(string $id, ?string $albumId = null): Track
    {
        return $this->getAlbumForTrack($id, $albumId)->getTrack($id);
    }

    public function removeTrack(string $id, ?string $albumId = null): self
    {
        $this->getAlbumForTrack($id, $albumId)->removeTrack($id);

        return $this;
    }

    public function removeAlbum(string $id): self
    {
        $this->albums->forget($id);

        return $this;
    }

    public function applyReciterNameChanged(ReciterNameChanged $event): self
    {
        $this->name = $event->name;

        return $this;
    }

    public function applyReciterDescriptionChanged(ReciterDescriptionChanged $event): self
    {
        $this->description = $event->description;

        return $this;
    }

    public function applyReciterAvatarChanged(ReciterAvatarChanged $event): self
    {
        $this->avatar = $event->avatar;

        return $this;
    }

    public function applyAlbumCreated(AlbumCreated $event): self
    {
        $data = collect($event->attributes);

        $album = new AlbumEntity(
            $event->id,
            $data->get('title'),
            $data->get('year'),
            $data->get('artwork'),
        );

        $this->addAlbum($album);

        return $this;
    }

    public function applyAlbumTitleChanged(AlbumTitleChanged $event): self
    {
        $album = $this->getAlbum($event->id);
        $album->title = $event->title;

        return $this;
    }

    public function applyAlbumYearChanged(AlbumYearChanged $event): self
    {
        $album = $this->getAlbum($event->id);
        $album->year = $event->year;

        return $this;
    }


    public function applyAlbumArtworkChanged(AlbumArtworkChanged $event): self
    {
        $album = $this->getAlbum($event->id);
        $album->artwork = $event->artwork;

        return $this;
    }

    public function applyAlbumDeleted(AlbumDeleted $event): self
    {
        $this->removeAlbum($event->id);

        return $this;
    }

    public function applyTrackCreated(TrackCreated $event): self
    {
        $album = $this->getAlbum($event->albumId);

        $data = collect($event->attributes);

        $track = new TrackEntity(
            $event->id,
            $data->get('title'),
        );

        $album->addTrack($track);

        return $this;
    }

    public function applyTrackTitleChanged(TrackTitleChanged $event): self
    {
        $track = $this->getTrack($event->id);
        $track->title = $event->title;

        return $this;
    }

    public function applyTrackLyricsChanged(TrackLyricsChanged $event): self
    {
        $track = $this->getTrack($event->id);
        $track->lyrics = $event->document;

        return $this;
    }

    public function applyTrackAudioChanged(TrackAudioChanged $event): self
    {
        $track = $this->getTrack($event->id);
        $track->audio = $event->path;

        return $this;
    }

    public function applyTrackDeleted(TrackDeleted $event): self
    {
        $this->removeTrack($event->id);

        return $this;
    }

    public function apply($event): self
    {
        $baseName = (new ReflectionClass($event))->getShortName();
        $method = 'apply' . $baseName;

        if (!method_exists($this, $method)) {
            throw new \BadMethodCallException('No handler found for ' . $baseName);
        }

        $this->{$method}($event);

        return $this;
    }

    public function toSnapshot(): array
    {
        return [
            'id' => $this->id,
            'name'  => $this->name,
            'description' => $this->description,
            'avatar' => $this->avatar,
            'albums' => $this->albums->map(function (Album $album) {
                return [
                    'id' => $album->id,
                    'title' => $album->title,
                    'year' => $album->year,
                    'artwork' => $album->artwork,
                    'tracks' => $album->tracks->map(function (Track $track) {
                        return [
                            'id' => $track->id,
                            'title' => $track->title,
                            'audio' => $track->audio,
                            'lyrics' => optional($track->lyrics)->toArray(),
                        ];
                    }),
                ];
            })
        ];
    }
}
