<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Transformers;

use App\Modules\Core\Transformers\Transformer;
use App\Modules\Library\Enum\MediaProvider;
use App\Modules\Library\Enum\MediaType;
use Illuminate\Support\Facades\Storage;

class MediaTransformer extends Transformer
{
    public function toArray($media): array
    {
        return [
            'id' => '(deprecated)',
            'uri' => Storage::url($media),
            'type' => MediaType::Audio->value,
            'provider' => MediaProvider::File->value,
            'createdAt' => $this->dateTime(now()),
            'updatedAt' => $this->dateTime(now()),
        ];
    }
}
