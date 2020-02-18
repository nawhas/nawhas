<?php

declare(strict_types=1);

namespace App\Http\Transformers;

use App\Entities\Media;

class MediaTransformer extends Transformer
{
    public function toArray(Media $media): array
    {
        return [
            'id' => $media->getId(),
            'uri' => $media->getUri(),
            'type' => $media->getType(),
            $this->timestamps($media),
        ];
    }
}
