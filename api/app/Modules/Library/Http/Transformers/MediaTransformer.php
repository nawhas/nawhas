<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Transformers;

use App\Http\Transformers\Transformer;
use App\Modules\Library\Models\Media;
use Illuminate\Support\Facades\Storage;

class MediaTransformer extends Transformer
{
    public function toArray(Media $media): array
    {
        return [
            'id' => $media->id,
            'uri' => Storage::url($media->path),
            'type' => $media->type,
            'provider' => $media->provider,
            $this->timestamps($media),
        ];
    }
}
