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

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function assertTitle(string $title): self
    {
        $this->response->assertJsonPath('title', $title);

        return $this;
    }

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function assertSlug(string $slug): self
    {
        $this->response->assertJsonPath('slug', $slug);

        return $this;
    }

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function assertYear(string $year): self
    {
        $this->response->assertJsonPath('year', $year);

        return $this;
    }

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function assertOwnedBy(Reciter $reciter): self
    {
        $this->response->assertJsonPath('reciterId', $reciter->id);

        return $this;
    }

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function assertAlbum(Album $album): self
    {
        $this->response->assertJsonPath('albumId', $album->id);

        return $this;
    }

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function assertMatches(Track $track): self
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
