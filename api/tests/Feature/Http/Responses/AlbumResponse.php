<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;

class AlbumResponse extends Response
{
    protected function getJsonStructure(): array
    {
        return [
            'id',
            'reciterId',
            'title',
            'year',
            'artwork',
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
    public function assertMatches(Album $album): self
    {
        $this->response->assertJsonFragment([
            'id' => $album->id,
            'reciterId' => $album->reciter_id,
            'title' => $album->title,
            'year' => $album->year,
            'artwork' => $album->artwork,
        ]);

        return $this;
    }
}
