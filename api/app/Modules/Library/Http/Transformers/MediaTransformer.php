<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Transformers;

use App\Enum\MediaProvider;
use App\Enum\MediaType;
use App\Http\Transformers\Transformer;
use Illuminate\Support\Facades\Storage;

class MediaTransformer extends Transformer
{
    public function toArray($media): array
    {
        return [
            'id' => '(deprecated)',
            'uri' => Storage::url($media),
            'type' => MediaType::AUDIO,
            'provider' => MediaProvider::FILE,
            'createdAt' => $this->dateTime(now()),
            'updatedAt' => $this->dateTime(now()),
        ];
    }
}
