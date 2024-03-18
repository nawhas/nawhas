<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Transformers;

use App\Modules\Core\Transformers\Transformer;
use App\Modules\Lyrics\Documents\Document;

class LyricsTransformer extends Transformer
{
    public function toArray(Document $document): array
    {
        return [
            'id' => '(deprecated)',
            'trackId' => '(deprecated)',
            'content' => $document->getContent(),
            'format' => $document->getFormat()->value,
            'createdAt' => $this->dateTime(now()),
            'updatedAt' => $this->dateTime(now()),
        ];
    }
}
