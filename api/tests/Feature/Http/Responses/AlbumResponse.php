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

    public function assertTitle(string $title): static
    {
        $this->response->assertJsonPath('title', $title);

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

    public function assertMatches(Album $album): static
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
