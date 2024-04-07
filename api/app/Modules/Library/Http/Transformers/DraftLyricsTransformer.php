<?php

namespace App\Modules\Library\Http\Transformers;

use App\Modules\Core\Transformers\Transformer;
use App\Modules\Library\Models\DraftLyrics;
use League\Fractal\Resource\Primitive;

class DraftLyricsTransformer extends Transformer
{
    protected array $availableIncludes = ['track', 'related'];

    public function toArray(DraftLyrics $draftLyrics): array
    {
        return [
            'id' => $draftLyrics->id,
            'trackId' => $draftLyrics->track_id,
            'document' => $draftLyrics->document->toArray(),
            'createdAt' => $this->dateTime($draftLyrics->created_at),
            'updatedAt' => $this->dateTime($draftLyrics->updated_at)
        ];
    }

    public function includeTrack(DraftLyrics $draftLyrics)
    {
        return $this->item($draftLyrics->track->album, new TrackTransformer());
    }

    public function includeRelated(DraftLyrics $draftLyrics): Primitive
    {
        return $this->primitive([
            'track' => $draftLyrics->track != null
        ]);
    }
}
