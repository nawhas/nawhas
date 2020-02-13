<?php

declare(strict_types=1);

namespace App\Http\Transformers;

use App\Entities\Lyrics;

class LyricsTransformer extends Transformer
{
    public function toArray(Lyrics $lyrics): array
    {
        return [
            'id' => $lyrics->getId(),
            'trackId' => $lyrics->getTrack()->getId(),
            'content' => $lyrics->getContent(),
            $this->timestamps($lyrics),
        ];
    }
}
