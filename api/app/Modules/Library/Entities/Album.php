<?php

declare(strict_types=1);

namespace App\Modules\Library\Entities;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class Album
{
    public string $id;
    public string $title;
    public string $year;
    public ?string $artwork;

    public Collection $tracks;

    public function __construct(
        string $id,
        string $title,
        string $year,
        ?string $artwork = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->year = $year;
        $this->artwork = $artwork;
        $this->tracks = collect();
    }

    public function addTrack(Track $track): self
    {
        $this->tracks->put($track->id, $track);

        return $this;
    }

    public function removeTrack(string $id): self
    {
        $this->tracks->forget($id);

        return $this;
    }

    public function getTrack(string $id): Track
    {
        /** @var Track|null $track */
        $track = $this->tracks->get($id);

        if (!$track) {
            throw new ModelNotFoundException("Track#{$id} not found.");
        }

        return $track;
    }
}
