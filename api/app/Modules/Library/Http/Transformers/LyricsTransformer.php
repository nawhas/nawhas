<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Transformers;

use App\Http\Transformers\Transformer;
use App\Modules\Library\Models\Lyrics;

class LyricsTransformer extends Transformer
{
    public function toArray(Lyrics $lyrics): array
    {
        return [
            'id' => $lyrics->id,
            'trackId' => $lyrics->track_id,
            'content' => $lyrics->content,
            'format' => $lyrics->format,
            $this->timestamps($lyrics),
        ];
    }
}
