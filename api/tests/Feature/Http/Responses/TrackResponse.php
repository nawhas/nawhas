<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;

class TrackResponse extends Response
{
    protected function getJsonStructure(): array
    {
        return [
            'id',
            'reciterId',
            'albumId',
            'title',
            'slug',
            'year',
            'audio',
            'video',
            'createdAt',
            'updatedAt',
        ];
    }

    public function assertTitle(string $title): static
    {
        $this->response->assertJsonPath('title', $title);

        return $this;
    }

    public function assertSlug(string $slug): static
    {
        $this->response->assertJsonPath('slug', $slug);

        return $this;
    }

    public function assertYear(string $year): static
    {
        $this->response->assertJsonPath('year', $year);

        return $this;
    }

    public function assertOwnedBy(Reciter $reciter): static
    {
        $this->response->assertJsonPath('reciterId', $reciter->id);

        return $this;
    }

    public function assertAlbum(Album $album): static
    {
        $this->response->assertJsonPath('albumId', $album->id);

        return $this;
    }

    public function assertMatches(Track $track): static
    {
        $this->response->assertJsonFragment([
            'id' => $track->id,
            'reciterId' => $track->reciter_id,
            'albumId' => $track->album_id,
            'title' => $track->title,
            'slug' => $track->slug,
            'year' => $track->album->year,
            'audio' => $track->audio,
            'video' => $track->video,
        ]);

        return $this;
    }
}
