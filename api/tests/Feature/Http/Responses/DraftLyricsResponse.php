<?php

namespace Tests\Feature\Http\Responses;

use App\Modules\Library\Models\DraftLyrics;
use App\Modules\Lyrics\Documents\Document;

class DraftLyricsResponse extends Response
{
    protected function getJsonStructure(): array
    {
        return [
            'id',
            'trackId',
            'document',
            'createdAt',
            'updatedAt'
        ];
    }

    public function assertId(string $id): static
    {
        $this->response->assertJsonPath('id', $id);

        return $this;
    }

    public function assertTrackId(string $track): static
    {
        $this->response->assertJsonPath('trackId', $track);

        return $this;
    }

    public function assertDocument(Document $document): static
    {
        $this->response->assertJsonPath('document', $document->toArray());

        return $this;
    }

    public function assertMatches(DraftLyrics $draftLyrics): static
    {
        $this->response->assertJsonFragment([
            'id' => $draftLyrics->id,
            'trackId' => $draftLyrics->track_id,
            'document' => $draftLyrics->document->toArray(),
        ]);

        return $this;
    }
}
